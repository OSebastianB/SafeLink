<?php

namespace App\Services\Spk;

use App\Models\AppSetting;

class RiskClassificationService
{
    public function lowMax(): float
    {
        return AppSetting::getFloat('risk_low_max', 20.0);
    }

    public function mediumMax(): float
    {
        return AppSetting::getFloat('risk_medium_max', 50.0);
    }

    /**
     * Classify risk index into low / medium / high.
     * Default thresholds:
     *   0–20        => low
     *   >20–50      => medium
     *   >50–100     => high
     */
    public function classify(float $riskIndex): string
    {
        $riskIndex = max(0.0, min(100.0, $riskIndex));

        if ($riskIndex <= $this->lowMax()) {
            return 'low';
        }

        if ($riskIndex <= $this->mediumMax()) {
            return 'medium';
        }

        return 'high';
    }

    public function label(string $category): string
    {
        return match ($category) {
            'low' => 'Risiko Rendah',
            'medium' => 'Risiko Sedang',
            'high' => 'Risiko Tinggi',
            default => 'Tidak diketahui',
        };
    }
}
