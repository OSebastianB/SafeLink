<?php

namespace App\Services\Domain;

interface DomainAgeProviderInterface
{
    /**
     * Resolve domain age without connecting to the scanned host.
     * Implementations may only call allowlisted RDAP endpoints.
     *
     * @return array{
     *     age_days: int|null,
     *     created_at: string|null,
     *     score: float,
     *     evidence: string,
     *     data_source: string,
     *     data_status: string
     * }
     */
    public function lookup(string $domain): array;
}
