<?php

namespace App\Services\Scan;

use App\Models\Scan;
use App\Models\ScanCriterionResult;
use App\Models\User;
use App\Services\Spk\CriteriaScoringService;
use App\Services\Spk\SawCalculationService;
use App\Services\Url\UrlFeatureExtractionService;
use App\Services\Url\UrlParserService;
use App\Services\Url\UrlRiskIndicatorService;
use Illuminate\Support\Facades\DB;

class UrlScanService
{
    public function __construct(
        private readonly UrlParserService $parser,
        private readonly UrlFeatureExtractionService $featureExtractor,
        private readonly CriteriaScoringService $scoringService,
        private readonly SawCalculationService $sawService,
        private readonly UrlRiskIndicatorService $indicatorService
    ) {
    }

    public function scan(string $url, ?User $user = null): Scan
    {
        $parsed = $this->parser->parse($url);
        $features = $this->featureExtractor->extract($parsed);

        // Security note: we intentionally never HTTP-request $parsed['normalized_url'].
        $scored = $this->scoringService->scoreAll($parsed, $features);
        $saw = $this->sawService->calculate(
            $scored['results'],
            null,
            $scored['threat_match_status']
        );

        $explanation = $this->indicatorService->buildExplanation($saw);
        $warning = $saw['warnings'] !== [] ? implode(' ', $saw['warnings']) : null;

        return DB::transaction(function () use ($parsed, $features, $saw, $explanation, $warning, $user) {
            $scan = Scan::query()->create([
                'user_id' => $user?->id,
                'original_url' => $parsed['original_url'],
                'normalized_url' => $parsed['normalized_url'],
                'domain' => $parsed['domain'],
                'protocol' => $parsed['scheme'],
                'security_score' => $saw['security_score'],
                'risk_index' => $saw['risk_index'],
                'risk_category' => $saw['risk_category'],
                'data_coverage_percentage' => $saw['data_coverage'],
                'threat_match_status' => $saw['threat_match_status'],
                'warning_message' => $warning,
                'explanation' => $explanation,
                'url_features' => $features,
                'scanned_at' => now(),
            ]);

            foreach ($saw['criteria_results'] as $row) {
                ScanCriterionResult::query()->create([
                    'scan_id' => $scan->id,
                    'criterion_id' => $row['criterion_id'],
                    'criterion_code_snapshot' => $row['criterion_code_snapshot'],
                    'criterion_name_snapshot' => $row['criterion_name_snapshot'],
                    'criterion_weight_snapshot' => $row['criterion_weight_snapshot'],
                    'raw_value' => $row['raw_value'],
                    'score' => $row['score'],
                    'normalized_value' => $row['normalized_value'],
                    'weighted_contribution' => $row['weighted_contribution'],
                    'evidence' => $row['evidence'],
                    'data_source' => $row['data_source'],
                    'data_status' => $row['data_status'],
                ]);
            }

            return $scan->load('criterionResults');
        });
    }
}
