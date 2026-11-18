<?php

namespace App\Services\Evaluation;

use App\Models\EvaluationDataset;
use App\Services\Scan\UrlScanService;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DatasetEvaluationService
{
    public function __construct(
        private readonly UrlScanService $scanService
    ) {
    }

    /**
     * Binary mapping rule (explicit for academic documentation):
     * - High Risk  => predicted phishing
     * - Low/Medium => predicted legitimate
     *
     * @return array<string, mixed>
     */
    public function evaluate(EvaluationDataset $dataset, string $mode = 'binary'): array
    {
        $rows = $dataset->rows;
        $yTrue = [];
        $yPred = [];
        $details = [];
        $errors = [];

        foreach ($rows as $row) {
            try {
                $scan = $this->scanService->scan($row->url, Auth::user());
                $label = strtolower($row->ground_truth_label);
                $predCategory = $scan->risk_category;

                if ($mode === 'binary') {
                    $trueBinary = $label === 'phishing' ? 'phishing' : 'legitimate';
                    $predBinary = $predCategory === 'high' ? 'phishing' : 'legitimate';
                    $yTrue[] = $trueBinary;
                    $yPred[] = $predBinary;
                    $details[] = [
                        'url' => $row->url,
                        'ground_truth' => $trueBinary,
                        'predicted' => $predBinary,
                        'risk_category' => $predCategory,
                        'risk_index' => $scan->risk_index,
                        'correct' => $trueBinary === $predBinary,
                    ];
                } else {
                    // Three-class: map ground truth phishing->high, legitimate->low (medium not in labels).
                    $trueClass = $label === 'phishing' ? 'high' : 'low';
                    $yTrue[] = $trueClass;
                    $yPred[] = $predCategory;
                    $details[] = [
                        'url' => $row->url,
                        'ground_truth' => $trueClass,
                        'predicted' => $predCategory,
                        'risk_category' => $predCategory,
                        'risk_index' => $scan->risk_index,
                        'correct' => $trueClass === $predCategory,
                    ];
                }
            } catch (Throwable $e) {
                $errors[] = [
                    'url' => $row->url,
                    'error' => $e->getMessage(),
                ];
            }
        }

        if ($mode === 'binary') {
            $metrics = $this->binaryMetrics($yTrue, $yPred);
        } else {
            $metrics = $this->multiclassMetrics($yTrue, $yPred, ['low', 'medium', 'high']);
        }

        return array_merge($metrics, [
            'mode' => $mode,
            'total_rows' => $rows->count(),
            'evaluated' => count($details),
            'errors' => $errors,
            'details' => $details,
            'binary_rule' => 'High Risk = phishing; Low Risk dan Medium Risk = legitimate',
        ]);
    }

    /**
     * @param  list<string>  $yTrue
     * @param  list<string>  $yPred
     * @return array<string, mixed>
     */
    public function binaryMetrics(array $yTrue, array $yPred): array
    {
        $tp = $tn = $fp = $fn = 0;

        foreach ($yTrue as $i => $true) {
            $pred = $yPred[$i] ?? null;
            if ($true === 'phishing' && $pred === 'phishing') {
                $tp++;
            } elseif ($true === 'legitimate' && $pred === 'legitimate') {
                $tn++;
            } elseif ($true === 'legitimate' && $pred === 'phishing') {
                $fp++;
            } elseif ($true === 'phishing' && $pred === 'legitimate') {
                $fn++;
            }
        }

        $accuracy = $this->safeDiv($tp + $tn, $tp + $tn + $fp + $fn);
        $precision = $this->safeDiv($tp, $tp + $fp);
        $recall = $this->safeDiv($tp, $tp + $fn);
        $f1 = $this->safeDiv(2 * $precision * $recall, $precision + $recall);

        return [
            'confusion_matrix' => [
                'tp' => $tp,
                'tn' => $tn,
                'fp' => $fp,
                'fn' => $fn,
            ],
            'correct' => $tp + $tn,
            'incorrect' => $fp + $fn,
            'accuracy' => $accuracy,
            'precision' => $precision,
            'recall' => $recall,
            'f1_score' => $f1,
        ];
    }

    /**
     * @param  list<string>  $yTrue
     * @param  list<string>  $yPred
     * @param  list<string>  $labels
     * @return array<string, mixed>
     */
    public function multiclassMetrics(array $yTrue, array $yPred, array $labels): array
    {
        $matrix = [];
        foreach ($labels as $a) {
            foreach ($labels as $b) {
                $matrix[$a][$b] = 0;
            }
        }

        $correct = 0;
        foreach ($yTrue as $i => $true) {
            $pred = $yPred[$i] ?? null;
            if (! in_array($true, $labels, true) || ! in_array($pred, $labels, true)) {
                continue;
            }
            $matrix[$true][$pred]++;
            if ($true === $pred) {
                $correct++;
            }
        }

        $total = count($yTrue);
        $precisionSum = 0.0;
        $recallSum = 0.0;
        $f1Sum = 0.0;
        $counted = 0;

        foreach ($labels as $label) {
            $tp = $matrix[$label][$label] ?? 0;
            $fp = 0;
            $fn = 0;
            foreach ($labels as $other) {
                if ($other !== $label) {
                    $fp += $matrix[$other][$label] ?? 0;
                    $fn += $matrix[$label][$other] ?? 0;
                }
            }
            $precision = $this->safeDiv($tp, $tp + $fp);
            $recall = $this->safeDiv($tp, $tp + $fn);
            $f1 = $this->safeDiv(2 * $precision * $recall, $precision + $recall);
            $precisionSum += $precision;
            $recallSum += $recall;
            $f1Sum += $f1;
            $counted++;
        }

        return [
            'confusion_matrix' => $matrix,
            'correct' => $correct,
            'incorrect' => max(0, $total - $correct),
            'accuracy' => $this->safeDiv($correct, max(1, $total)),
            'precision' => $counted ? $precisionSum / $counted : 0.0,
            'recall' => $counted ? $recallSum / $counted : 0.0,
            'f1_score' => $counted ? $f1Sum / $counted : 0.0,
        ];
    }

    private function safeDiv(float $a, float $b): float
    {
        if ($b == 0.0) {
            return 0.0;
        }

        return $a / $b;
    }
}
