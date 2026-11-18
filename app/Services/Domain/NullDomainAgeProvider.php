<?php

namespace App\Services\Domain;

/**
 * Safe fallback when RDAP/WHOIS providers are disabled or unavailable.
 * Does not fabricate registration dates.
 */
class NullDomainAgeProvider implements DomainAgeProviderInterface
{
    public function lookup(string $domain): array
    {
        return [
            'age_days' => null,
            'created_at' => null,
            'score' => 2.5,
            'evidence' => 'Data usia domain tidak tersedia. Skor netral digunakan.',
            'data_source' => 'null_domain_age_provider',
            'data_status' => 'unavailable',
        ];
    }
}
