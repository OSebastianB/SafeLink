<?php

namespace App\Services\Reputation;

use App\Models\ThreatIntelligence;

class LocalThreatIntelligenceProvider implements ThreatIntelligenceProviderInterface
{
    public function name(): string
    {
        return 'local_threat_intelligence';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function lookup(string $domain, ?string $host = null): array
    {
        $domain = strtolower(trim($domain));
        $host = strtolower(trim((string) $host));

        $candidates = array_values(array_unique(array_filter([$host, $domain])));

        $record = ThreatIntelligence::query()
            ->valid()
            ->whereIn('domain', $candidates)
            ->orderByRaw("CASE WHEN status IN ('phishing','malicious') THEN 0 ELSE 1 END")
            ->orderByDesc('verified_at')
            ->first();

        if (! $record) {
            // Tidak ditemukan ≠ aman. Kembalikan unknown tanpa mengarang reputasi.
            return [
                'status' => ThreatIntelligence::STATUS_UNKNOWN,
                'threat_type' => null,
                'source' => $this->name(),
                'score' => 2.5,
                'evidence' => 'Reputasi tidak diketahui atau sumber lokal tidak memiliki catatan untuk domain ini.',
                'data_status' => 'unavailable',
                'verified_at' => null,
                'is_confirmed_threat' => false,
            ];
        }


        return match ($record->status) {
            ThreatIntelligence::STATUS_MALICIOUS,
            ThreatIntelligence::STATUS_PHISHING => [
                'status' => $record->status,
                'threat_type' => $record->threat_type,
                'source' => $record->source ?: $this->name(),
                'score' => 0.0,
                'evidence' => 'Ditemukan indikasi '.($record->threat_type ?: $record->status).' yang terkonfirmasi dari sumber tepercaya ('.$record->source.').',
                'data_status' => 'available',
                'verified_at' => optional($record->verified_at)?->toDateTimeString(),
                'is_confirmed_threat' => true,
            ],
            ThreatIntelligence::STATUS_SUSPICIOUS => [
                'status' => $record->status,
                'threat_type' => $record->threat_type,
                'source' => $record->source ?: $this->name(),
                'score' => 2.0,
                'evidence' => 'Domain tercatat sebagai mencurigakan pada basis data lokal.',
                'data_status' => 'available',
                'verified_at' => optional($record->verified_at)?->toDateTimeString(),
                'is_confirmed_threat' => false,
            ],
            ThreatIntelligence::STATUS_NO_MATCH => [
                'status' => $record->status,
                'threat_type' => $record->threat_type,
                'source' => $record->source ?: $this->name(),
                'score' => 4.0,
                'evidence' => 'Ditemukan pada hasil pencarian lokal yang tidak menunjukkan kecocokan ancaman. Ini bukan bukti bahwa domain aman.',
                'data_status' => 'available',
                'verified_at' => optional($record->verified_at)?->toDateTimeString(),
                'is_confirmed_threat' => false,
            ],
            default => [
                'status' => ThreatIntelligence::STATUS_UNKNOWN,
                'threat_type' => $record->threat_type,
                'source' => $record->source ?: $this->name(),
                'score' => 2.5,
                'evidence' => 'Status reputasi tercatat sebagai tidak diketahui.',
                'data_status' => 'unavailable',
                'verified_at' => optional($record->verified_at)?->toDateTimeString(),
                'is_confirmed_threat' => false,
            ],
        };
    }
}
