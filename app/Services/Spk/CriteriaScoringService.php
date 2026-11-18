<?php

namespace App\Services\Spk;

use App\Models\BrandWatchlist;
use App\Models\Criterion;
use App\Services\Domain\DomainAgeProviderInterface;
use App\Services\Reputation\ThreatIntelligenceProviderInterface;
use App\Services\Url\UrlParserService;

class CriteriaScoringService
{
    public function __construct(
        private readonly ThreatIntelligenceProviderInterface $reputationProvider,
        private readonly DomainAgeProviderInterface $domainAgeProvider,
        private readonly UrlParserService $urlParser
    ) {
    }

    /**
     * Score all active criteria for a parsed URL + features.
     *
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $features
     * @return array{results: list<array<string, mixed>>, threat_match_status: string|null}
     */
    public function scoreAll(array $parsed, array $features): array
    {
        $criteria = Criterion::active()->get();
        $results = [];
        $threatMatchStatus = null;

        foreach ($criteria as $criterion) {
            $scored = match ($criterion->code) {
                'C1' => $this->scoreReputation($criterion, $parsed),
                'C2' => $this->scoreDomainAge($criterion, $parsed),
                'C3' => $this->scoreHttps($criterion, $parsed),
                'C4' => $this->scoreUrlStructure($criterion, $features),
                'C5' => $this->scoreIpUsage($criterion, $features),
                'C6' => $this->scorePhishingIndicators($criterion, $parsed, $features),
                default => $this->neutral($criterion, 'Kriteria khusus belum memiliki aturan penilaian otomatis.'),
            };

            if (($scored['is_confirmed_threat'] ?? false) === true) {
                $threatMatchStatus = $scored['threat_status'] ?? 'phishing';
            }

            unset($scored['is_confirmed_threat'], $scored['threat_status']);
            $results[] = $scored;
        }

        return [
            'results' => $results,
            'threat_match_status' => $threatMatchStatus,
        ];
    }

    private function scoreReputation(Criterion $criterion, array $parsed): array
    {
        $domain = $parsed['registrable_domain'] ?: $parsed['domain'];
        $host = $parsed['host'] ?? $parsed['domain'] ?? null;

        $lookup = $this->reputationProvider->lookup($domain, $host);


        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => (float) $lookup['score'],
            'raw_value' => $lookup['status'],
            'evidence' => $lookup['evidence'],
            'data_source' => $lookup['source'],
            'data_status' => $lookup['data_status'],
            'is_confirmed_threat' => $lookup['is_confirmed_threat'],
            'threat_status' => $lookup['status'],
        ];
    }

    private function scoreDomainAge(Criterion $criterion, array $parsed): array
    {
        if (! empty($parsed['is_ip'])) {
            return [
                'criterion_id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => (float) $criterion->weight,
                'score' => 2.5,
                'raw_value' => 'ip_host',
                'evidence' => 'Host menggunakan alamat IP; usia domain tidak relevan/tidak tersedia.',
                'data_source' => 'domain_age_analyzer',
                'data_status' => 'unavailable',
            ];
        }

        $lookup = $this->domainAgeProvider->lookup($parsed['registrable_domain']);

        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => (float) $lookup['score'],
            'raw_value' => $lookup['created_at'] ?? 'unavailable',
            'evidence' => $lookup['evidence'],
            'data_source' => $lookup['data_source'],
            'data_status' => $lookup['data_status'],
        ];
    }

    private function scoreHttps(Criterion $criterion, array $parsed): array
    {
        $scheme = strtolower((string) ($parsed['scheme'] ?? ''));

        if ($scheme === 'https') {
            return [
                'criterion_id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => (float) $criterion->weight,
                'score' => 4.0,
                'raw_value' => 'https',
                'evidence' => 'URL menggunakan skema HTTPS. Penilaian ini hanya berdasarkan teks skema URL, bukan validasi sertifikat SSL.',
                'data_source' => 'url_scheme_parser',
                'data_status' => 'available',
            ];
        }

        if ($scheme === 'http') {
            return [
                'criterion_id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => (float) $criterion->weight,
                'score' => 2.0,
                'raw_value' => 'http',
                'evidence' => 'URL menggunakan skema HTTP tanpa enkripsi transport.',
                'data_source' => 'url_scheme_parser',
                'data_status' => 'available',
            ];
        }

        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => 2.5,
            'raw_value' => 'unknown',
            'evidence' => 'Skema protokol tidak diketahui.',
            'data_source' => 'url_scheme_parser',
            'data_status' => 'unavailable',
        ];
    }

    /**
     * Deterministic URL structure scoring (C4).
     *
     * Heavy anomalies: @ symbol, extreme length (>150), many encodings (>=5), IP-like deception in path
     * Light anomalies: long URL (>75), subdomain>=3, hyphen>=3, suspicious TLD, path depth>=4, digit ratio>0.3
     *
     * Scoring:
     * 0 heavy & 0 light => 5
     * 0 heavy & 1 light => 4
     * 0 heavy & 2-3 light => 3
     * 1 heavy or >=4 light => 2
     * >=2 heavy => 1
     */
    private function scoreUrlStructure(Criterion $criterion, array $features): array
    {
        $light = [];
        $heavy = [];

        if (! empty($features['has_at_symbol'])) {
            $heavy[] = 'mengandung karakter @';
        }
        if (($features['url_length'] ?? 0) > 150) {
            $heavy[] = 'panjang URL ekstrem (>150)';
        }
        if (($features['encoded_char_count'] ?? 0) >= 5) {
            $heavy[] = 'encoding karakter berlebihan';
        }

        if (($features['url_length'] ?? 0) > 75 && ($features['url_length'] ?? 0) <= 150) {
            $light[] = 'URL relatif panjang';
        }
        if (($features['subdomain_count'] ?? 0) >= 3) {
            $light[] = 'jumlah subdomain tinggi';
        }
        if (($features['hyphen_count_in_host'] ?? 0) >= 3) {
            $light[] = 'banyak tanda hubung pada host';
        }
        if (! empty($features['suspicious_tld'])) {
            $light[] = 'TLD yang sering disalahgunakan';
        }
        if (($features['path_depth'] ?? 0) >= 4) {
            $light[] = 'kedalaman path tinggi';
        }
        if (($features['digit_ratio_in_host'] ?? 0) > 0.3) {
            $light[] = 'rasio digit pada host tinggi';
        }

        $heavyCount = count($heavy);
        $lightCount = count($light);

        if ($heavyCount >= 2) {
            $score = 1.0;
            $label = 'Terdapat beberapa anomali berat';
        } elseif ($heavyCount === 1 || $lightCount >= 4) {
            $score = 2.0;
            $label = 'Terdapat pola struktur yang mencurigakan';
        } elseif ($lightCount >= 2) {
            $score = 3.0;
            $label = 'Terdapat beberapa anomali ringan';
        } elseif ($lightCount === 1) {
            $score = 4.0;
            $label = 'Terdapat satu anomali ringan';
        } else {
            $score = 5.0;
            $label = 'Struktur normal tanpa anomali berarti';
        }

        $details = array_merge(
            array_map(fn ($x) => 'berat: '.$x, $heavy),
            array_map(fn ($x) => 'ringan: '.$x, $light)
        );

        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => $score,
            'raw_value' => $label,
            'evidence' => $label.(count($details) ? ' ('.implode('; ', $details).')' : ''),
            'data_source' => 'url_structure_analyzer',
            'data_status' => 'available',
        ];
    }

    private function scoreIpUsage(Criterion $criterion, array $features): array
    {
        if (! empty($features['has_ip_host'])) {
            return [
                'criterion_id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => (float) $criterion->weight,
                'score' => 1.0,
                'raw_value' => 'ip_'.($features['ip_version'] ?? 'unknown'),
                'evidence' => 'Host URL menggunakan alamat IP secara langsung ('.strtoupper((string) ($features['ip_version'] ?? 'IP')).').',
                'data_source' => 'ip_host_detector',
                'data_status' => 'available',
            ];
        }

        if (empty($features['host'])) {
            return [
                'criterion_id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => (float) $criterion->weight,
                'score' => 2.5,
                'raw_value' => 'unanalyzable',
                'evidence' => 'Data host tidak dapat dianalisis.',
                'data_source' => 'ip_host_detector',
                'data_status' => 'unavailable',
            ];
        }

        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => 5.0,
            'raw_value' => 'domain_name',
            'evidence' => 'Host menggunakan nama domain yang valid, bukan alamat IP langsung.',
            'data_source' => 'ip_host_detector',
            'data_status' => 'available',
        ];
    }

    private function scorePhishingIndicators(Criterion $criterion, array $parsed, array $features): array
    {
        $keywords = $features['suspicious_keywords'] ?? [];
        $keywordCount = count($keywords);
        $brandHits = $this->detectBrandImpersonation($parsed, $features);

        $strongImpersonation = $brandHits['strong'];
        $weakImpersonation = $brandHits['weak'];

        if ($strongImpersonation) {
            $score = 1.0;
            $label = 'Terdapat indikasi impersonasi yang sangat kuat';
        } elseif ($weakImpersonation || ($keywordCount >= 3 && ! empty($brandHits['mentions']))) {
            $score = 2.0;
            $label = 'Terdapat indikasi peniruan brand atau kombinasi pola kuat';
        } elseif ($keywordCount >= 2) {
            $score = 3.0;
            $label = 'Ditemukan beberapa kata atau pola mencurigakan';
        } elseif ($keywordCount === 1) {
            $score = 4.0;
            $label = 'Ditemukan satu indikator ringan';
        } else {
            $score = 5.0;
            $label = 'Tidak ditemukan indikator mencurigakan';
        }

        $parts = [];
        if ($keywordCount > 0) {
            $parts[] = 'kata kunci: '.implode(', ', $keywords);
        }
        if (! empty($brandHits['mentions'])) {
            $parts[] = 'kecocokan brand: '.implode(', ', $brandHits['mentions']);
        }

        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => $score,
            'raw_value' => $label,
            'evidence' => $label.(count($parts) ? ' ('.implode('; ', $parts).')' : '').'. Keberadaan kata login/verify saja tidak otomatis berarti phishing.',
            'data_source' => 'phishing_indicator_analyzer',
            'data_status' => 'available',
        ];
    }

    /**
     * @return array{strong: bool, weak: bool, mentions: list<string>}
     */
    private function detectBrandImpersonation(array $parsed, array $features): array
    {
        $brands = BrandWatchlist::active()->get();
        $host = strtolower((string) ($parsed['host'] ?? ''));
        $registrable = strtolower((string) ($parsed['registrable_domain'] ?? ''));
        $url = strtolower((string) ($parsed['normalized_url'] ?? ''));

        $mentions = [];
        $strong = false;
        $weak = false;

        foreach ($brands as $brand) {
            $official = strtolower($brand->official_domain);
            $officialRegistrable = $this->urlParser->extractRegistrableDomain($official);
            $name = strtolower($brand->brand_name);
            $keywords = array_map('strtolower', $brand->keywords ?? []);

            // Exact official domain / subdomain of official => not impersonation.
            if ($registrable === $officialRegistrable) {
                continue;
            }

            $nameInHost = $name !== '' && str_contains($host, str_replace(' ', '', $name));
            $keywordHit = false;
            foreach ($keywords as $kw) {
                if ($kw !== '' && (str_contains($host, $kw) || str_contains($url, $kw))) {
                    $keywordHit = true;
                    break;
                }
            }

            if ($nameInHost || $keywordHit) {
                $mentions[] = $brand->brand_name;

                // Strong: brand token in subdomain/host but registrable domain differs.
                if ($nameInHost && $registrable !== $officialRegistrable) {
                    $strong = true;
                } else {
                    $weak = true;
                }
            }
        }

        return [
            'strong' => $strong,
            'weak' => $weak,
            'mentions' => array_values(array_unique($mentions)),
        ];
    }

    private function neutral(Criterion $criterion, string $evidence): array
    {
        return [
            'criterion_id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'weight' => (float) $criterion->weight,
            'score' => 2.5,
            'raw_value' => 'unavailable',
            'evidence' => $evidence,
            'data_source' => 'system',
            'data_status' => 'unavailable',
        ];
    }
}
