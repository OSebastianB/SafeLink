<?php

namespace App\Services\Url;

/**
 * Helper for documenting and summarizing risk indicators used in explanations.
 */
class UrlRiskIndicatorService
{
    /**
     * Build a human-readable explanation from SAW result data.
     *
     * @param  array<string, mixed>  $sawResult
     */
    public function buildExplanation(array $sawResult): string
    {
        $category = $sawResult['risk_category'] ?? 'unknown';
        $coverage = (float) ($sawResult['data_coverage'] ?? 0);
        $threat = $sawResult['threat_match_status'] ?? null;

        $categoryText = match ($category) {
            'low' => 'risiko rendah',
            'medium' => 'risiko sedang',
            'high' => 'risiko tinggi',
            default => 'risiko tidak diketahui',
        };

        $parts = ["URL ini memperoleh kategori {$categoryText}."];

        $lowScores = [];
        foreach ($sawResult['criteria_results'] ?? [] as $row) {
            if ((float) $row['score'] <= 2.0) {
                $lowScores[] = $row['criterion_name_snapshot'];
            }
        }

        if ($lowScores !== []) {
            $parts[] = 'Terdapat karakteristik yang memerlukan perhatian pada: '.implode(', ', $lowScores).'.';
        }

        $unavailable = [];
        foreach ($sawResult['criteria_results'] ?? [] as $row) {
            if (($row['data_status'] ?? '') === 'unavailable') {
                $unavailable[] = $row['criterion_name_snapshot'];
            }
        }

        if ($unavailable !== []) {
            $parts[] = 'Data berikut belum tersedia: '.implode(', ', $unavailable).'.';
        }

        if ($coverage < 50) {
            $parts[] = 'Cakupan data masih rendah sehingga penilaian kurang andal.';
        }

        if (in_array($threat, ['malicious', 'phishing'], true)) {
            $parts[] = 'Sumber intelijen keamanan mengonfirmasi indikasi ancaman pada domain ini (peringatan prioritas di luar agregasi SAW).';
        }

        $parts[] = 'Hasil ini merupakan estimasi berbasis indikator, bukan jaminan mutlak keamanan.';

        return implode(' ', $parts);
    }
}
