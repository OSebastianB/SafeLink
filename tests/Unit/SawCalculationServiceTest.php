<?php

namespace Tests\Unit;

use App\Models\AppSetting;
use App\Services\Spk\RiskClassificationService;
use App\Services\Spk\SawCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SawCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function sampleCriteria(array $overrides = []): array
    {
        $base = [
            ['code' => 'C1', 'name' => 'Reputasi Domain', 'weight' => 0.30, 'score' => 4.0, 'data_status' => 'available'],
            ['code' => 'C2', 'name' => 'Usia Domain', 'weight' => 0.15, 'score' => 2.5, 'data_status' => 'unavailable'],
            ['code' => 'C3', 'name' => 'HTTPS', 'weight' => 0.10, 'score' => 4.0, 'data_status' => 'available'],
            ['code' => 'C4', 'name' => 'Struktur', 'weight' => 0.15, 'score' => 5.0, 'data_status' => 'available'],
            ['code' => 'C5', 'name' => 'IP', 'weight' => 0.10, 'score' => 5.0, 'data_status' => 'available'],
            ['code' => 'C6', 'name' => 'Phishing', 'weight' => 0.20, 'score' => 5.0, 'data_status' => 'available'],
        ];

        foreach ($overrides as $code => $values) {
            foreach ($base as &$row) {
                if ($row['code'] === $code) {
                    $row = array_merge($row, $values);
                }
            }
        }

        return $base;
    }

    public function test_total_weight_must_be_one(): void
    {
        AppSetting::setValue('risk_low_max', 20);
        AppSetting::setValue('risk_medium_max', 50);

        $service = app(SawCalculationService::class);
        $result = $service->calculate($this->sampleCriteria());

        $this->assertEqualsWithDelta(1.0, $result['total_weight'], 0.0001);
    }

    public function test_normalization_and_preference_match_manual_calculation(): void
    {
        AppSetting::setValue('risk_low_max', 20);
        AppSetting::setValue('risk_medium_max', 50);

        $criteria = $this->sampleCriteria();
        $manual = 0.0;
        foreach ($criteria as $item) {
            $manual += ($item['score'] / 5.0) * $item['weight'];
        }

        $result = app(SawCalculationService::class)->calculate($criteria);

        $this->assertEqualsWithDelta($manual, $result['security_score'], 1e-9);
        $this->assertEqualsWithDelta((1 - $manual) * 100, $result['risk_index'], 1e-9);

        foreach ($result['criteria_results'] as $row) {
            $this->assertEqualsWithDelta($row['score'] / 5.0, $row['normalized_value'], 1e-9);
            $this->assertEqualsWithDelta(($row['score'] / 5.0) * $row['criterion_weight_snapshot'], $row['weighted_contribution'], 1e-9);
        }
    }

    public function test_risk_category_thresholds(): void
    {
        AppSetting::setValue('risk_low_max', 20);
        AppSetting::setValue('risk_medium_max', 50);
        $classifier = app(RiskClassificationService::class);

        $this->assertSame('low', $classifier->classify(0));
        $this->assertSame('low', $classifier->classify(20));
        $this->assertSame('medium', $classifier->classify(20.01));
        $this->assertSame('medium', $classifier->classify(50));
        $this->assertSame('high', $classifier->classify(50.01));
        $this->assertSame('high', $classifier->classify(100));
    }

    public function test_zero_score_handling(): void
    {
        AppSetting::setValue('risk_low_max', 20);
        AppSetting::setValue('risk_medium_max', 50);

        $criteria = $this->sampleCriteria(['C1' => ['score' => 0.0, 'data_status' => 'available']]);
        $result = app(SawCalculationService::class)->calculate($criteria, null, 'phishing');

        $this->assertEqualsWithDelta(0.0, $result['criteria_results'][0]['normalized_value'], 1e-9);
        $this->assertSame('phishing', $result['threat_match_status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertTrue(collect($result['warnings'])->contains(fn ($w) => str_contains($w, 'Peringatan prioritas')));
    }

    public function test_decimal_score_and_unavailable_data(): void
    {
        AppSetting::setValue('risk_low_max', 20);
        AppSetting::setValue('risk_medium_max', 50);

        $criteria = $this->sampleCriteria([
            'C2' => ['score' => 2.5, 'data_status' => 'unavailable'],
            'C3' => ['score' => 2.5, 'data_status' => 'unavailable'],
            'C4' => ['score' => 2.5, 'data_status' => 'unavailable'],
            'C5' => ['score' => 2.5, 'data_status' => 'unavailable'],
        ]);

        $result = app(SawCalculationService::class)->calculate($criteria);

        $this->assertLessThan(50.0, $result['data_coverage']);
        $this->assertTrue(collect($result['warnings'])->contains(fn ($w) => str_contains($w, 'Data belum cukup')));
    }

    public function test_invalid_total_weight_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $bad = $this->sampleCriteria(['C1' => ['weight' => 0.50]]);
        app(SawCalculationService::class)->calculate($bad);
    }
}
