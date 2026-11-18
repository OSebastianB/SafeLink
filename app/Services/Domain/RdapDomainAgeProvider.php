<?php

namespace App\Services\Domain;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Optional RDAP lookup against allowlisted bootstrap endpoints only.
 * Never connects to the user-supplied host as a request target.
 */
class RdapDomainAgeProvider implements DomainAgeProviderInterface
{
    /**
     * Allowlisted RDAP base URLs (IANA-related / common registries).
     *
     * @var list<string>
     */
    private array $allowlist = [
        'https://rdap.org/domain/',
        'https://rdap.verisign.com/com/v1/domain/',
        'https://rdap.verisign.com/net/v1/domain/',
        'https://rdap.nic.google/domain/',
    ];

    public function __construct(
        private readonly bool $enabled = false
    ) {
    }

    public function lookup(string $domain): array
    {
        if (! $this->enabled || ! config('safelink.rdap_enabled', false)) {
            return (new NullDomainAgeProvider)->lookup($domain);
        }

        $domain = strtolower(trim($domain));

        if ($domain === '' || filter_var($domain, FILTER_VALIDATE_IP)) {
            return (new NullDomainAgeProvider)->lookup($domain);
        }

        foreach ($this->allowlist as $base) {
            try {
                $response = Http::timeout((int) config('safelink.rdap_timeout', 5))
                    ->acceptJson()
                    ->get($base.$domain);

                if (! $response->successful()) {
                    continue;
                }

                $payload = $response->json();
                $created = $this->extractCreationDate($payload);

                if ($created === null) {
                    continue;
                }

                $ageDays = (int) now()->diffInDays($created, false) * -1;
                if ($ageDays < 0) {
                    $ageDays = 0;
                }

                return [
                    'age_days' => $ageDays,
                    'created_at' => $created->toDateString(),
                    'score' => $this->scoreFromAgeDays($ageDays),
                    'evidence' => 'Usia domain diperkirakan '.$this->humanAge($ageDays).' berdasarkan tanggal registrasi RDAP ('.$created->toDateString().').',
                    'data_source' => 'rdap:'.parse_url($base, PHP_URL_HOST),
                    'data_status' => 'available',
                ];
            } catch (Throwable $e) {
                Log::warning('RDAP lookup failed', [
                    'domain' => $domain,
                    'endpoint_host' => parse_url($base, PHP_URL_HOST),
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return (new NullDomainAgeProvider)->lookup($domain);
    }

    private function extractCreationDate(?array $payload): ?\Carbon\Carbon
    {
        if (! is_array($payload) || empty($payload['events'])) {
            return null;
        }

        foreach ($payload['events'] as $event) {
            $action = strtolower((string) ($event['eventAction'] ?? ''));
            if (in_array($action, ['registration', 'registered'], true) && ! empty($event['eventDate'])) {
                try {
                    return \Carbon\Carbon::parse($event['eventDate']);
                } catch (Throwable) {
                    return null;
                }
            }
        }

        return null;
    }

    public function scoreFromAgeDays(int $ageDays): float
    {
        if ($ageDays > 365 * 5) {
            return 5.0;
        }
        if ($ageDays >= 365 * 2) {
            return 4.0;
        }
        if ($ageDays >= 365) {
            return 3.0;
        }
        if ($ageDays >= 90) {
            return 2.0;
        }

        return 1.0;
    }

    private function humanAge(int $ageDays): string
    {
        if ($ageDays >= 365) {
            $years = round($ageDays / 365, 1);

            return $years.' tahun';
        }

        return $ageDays.' hari';
    }
}
