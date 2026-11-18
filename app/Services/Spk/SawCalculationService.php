<?php

namespace App\Services\Spk;

use InvalidArgumentException;

class SawCalculationService
{
    public const MAX_SCORE = 5.0;
    public const WEIGHT_TOLERANCE = 0.0001;

    /**
     * Calculate SAW preference score for a single URL alternative.
     *
     * Normalization (benefit, fixed reference scale):
     *   r(i,j) = x(i,j) / 5
     *
     * Preference:
     *   V(i) = Σ [w(j) × r(i,j)]
     *
     * Risk index:
     *   Risk Index = (1 - V(i)) × 100
     *
     * @param  array<int, array{
     *     criterion_id?: int|null,
     *     code: string,
     *     name: string,
     *     weight: float|int|string,
     *     score: float|int|string,
     *     raw_value?: string|null,
     *     evidence?: string|null,
     *     data_source?: string|null,
     *     data_status?: string
     * }>  $criteriaResults
     * @return array{
     *     criteria_results: array<int, array<string, mixed>>,
     *     total_weight: float,
     *     security_score: float,
     *     risk_index: float,
     *     risk_category: string,
     *     data_coverage: float,
     *     warnings: list<string>,
     *     threat_match_status: string|null
     * }
     */
    public function calculate(
        array $criteriaResults,
        ?RiskClassificationService $classifier = null,
        ?string $threatMatchStatus = null
    ): array {
        if ($criteriaResults === []) {
            throw new InvalidArgumentException('Tidak ada hasil kriteria untuk dihitung.');
        }

        $classifier ??= app(RiskClassificationService::class);

        $totalWeight = 0.0;
        $securityScore = 0.0;
        $availableCount = 0;
        $processed = [];
        $warnings = [];

        foreach ($criteriaResults as $item) {
            $weight = (float) $item['weight'];
            $score = (float) $item['score'];
            $dataStatus = $item['data_status'] ?? 'available';

            if ($weight < 0) {
                throw new InvalidArgumentException("Bobot kriteria {$item['code']} tidak boleh negatif.");
            }

            if ($score < 0 || $score > self::MAX_SCORE) {
                throw new InvalidArgumentException("Skor kriteria {$item['code']} harus berada pada rentang 0–5.");
            }

            $normalized = $score / self::MAX_SCORE;
            $contribution = $normalized * $weight;

            $totalWeight += $weight;
            $securityScore += $contribution;

            if ($dataStatus === 'unavailable') {
                $warnings[] = "Data kriteria {$item['code']} ({$item['name']}) tidak tersedia; digunakan skor netral.";
            } else {
                $availableCount++;
            }

            $processed[] = [
                'criterion_id' => $item['criterion_id'] ?? null,
                'criterion_code_snapshot' => $item['code'],
                'criterion_name_snapshot' => $item['name'],
                'criterion_weight_snapshot' => $weight,
                'raw_value' => $item['raw_value'] ?? null,
                'score' => $score,
                'normalized_value' => $normalized,
                'weighted_contribution' => $contribution,
                'evidence' => $item['evidence'] ?? null,
                'data_source' => $item['data_source'] ?? null,
                'data_status' => $dataStatus,
            ];
        }

        if (abs($totalWeight - 1.0) > self::WEIGHT_TOLERANCE) {
            throw new InvalidArgumentException(
                'Total bobot kriteria aktif harus tepat 1.00. Saat ini: '.number_format($totalWeight, 4)
            );
        }

        $securityScore = max(0.0, min(1.0, $securityScore));
        $riskIndex = (1.0 - $securityScore) * 100.0;
        $dataCoverage = (count($criteriaResults) > 0)
            ? ($availableCount / count($criteriaResults)) * 100.0
            : 0.0;

        if ($dataCoverage < 50.0) {
            $warnings[] = 'Data belum cukup untuk menghasilkan penilaian yang andal.';
        }

        if (in_array($threatMatchStatus, ['malicious', 'phishing'], true)) {
            $warnings[] = 'Peringatan prioritas: sumber intelijen keamanan mengonfirmasi indikasi ancaman pada URL/domain ini. Peringatan ini berada di luar agregasi SAW.';
        }

        $riskCategory = $classifier->classify($riskIndex);

        return [
            'criteria_results' => $processed,
            'total_weight' => $totalWeight,
            'security_score' => $securityScore,
            'risk_index' => $riskIndex,
            'risk_category' => $riskCategory,
            'data_coverage' => $dataCoverage,
            'warnings' => $warnings,
            'threat_match_status' => $threatMatchStatus,
        ];
    }
}
