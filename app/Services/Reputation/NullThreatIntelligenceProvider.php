<?php

namespace App\Services\Reputation;

use App\Models\ThreatIntelligence;

class NullThreatIntelligenceProvider implements ThreatIntelligenceProviderInterface
{
    public function name(): string
    {
        return 'null_provider';
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function lookup(string $domain, ?string $host = null): array
    {
        return [
            'status' => ThreatIntelligence::STATUS_UNKNOWN,
            'threat_type' => null,
            'source' => $this->name(),
            'score' => 2.5,
            'evidence' => 'Provider reputasi eksternal tidak aktif atau tidak tersedia.',
            'data_status' => 'unavailable',
            'verified_at' => null,
            'is_confirmed_threat' => false,
        ];
    }

}
