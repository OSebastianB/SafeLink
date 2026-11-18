<?php

namespace App\Services\Reputation;

interface ThreatIntelligenceProviderInterface
{
    /**
     * Lookup reputation for a domain without contacting the scanned URL itself.
     *
     * @return array{
     *     status: string,
     *     threat_type: string|null,
     *     source: string,
     *     score: float,
     *     evidence: string,
     *     data_status: string,
     *     verified_at: string|null,
     *     is_confirmed_threat: bool
     * }
     */
    public function lookup(string $domain, ?string $host = null): array;


    public function name(): string;

    public function isAvailable(): bool;
}
