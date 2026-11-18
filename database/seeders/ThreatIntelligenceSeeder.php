<?php

namespace Database\Seeders;

use App\Models\ThreatIntelligence;
use Illuminate\Database\Seeder;

class ThreatIntelligenceSeeder extends Seeder
{
    public function run(): void
    {
        // Synthetic demo records using reserved example domains only.
        $rows = [
            [
                'domain' => 'malware.example.com',
                'status' => ThreatIntelligence::STATUS_MALICIOUS,
                'threat_type' => 'malicious',
                'source' => 'synthetic_demo',
                'notes' => 'Data sintetis untuk demonstrasi. Bukan intelijen ancaman nyata.',
                'is_synthetic' => true,
            ],
            [
                'domain' => 'phish.example.net',
                'status' => ThreatIntelligence::STATUS_PHISHING,
                'threat_type' => 'phishing',
                'source' => 'synthetic_demo',
                'notes' => 'Data sintetis untuk demonstrasi. Bukan intelijen ancaman nyata.',
                'is_synthetic' => true,
            ],
            [
                'domain' => 'example.com',
                'status' => ThreatIntelligence::STATUS_NO_MATCH,
                'threat_type' => null,
                'source' => 'synthetic_demo',
                'notes' => 'Data sintetis: no_match. Tidak berarti domain terbukti aman.',
                'is_synthetic' => true,
            ],
        ];

        foreach ($rows as $row) {
            ThreatIntelligence::query()->updateOrCreate(
                [
                    'domain' => $row['domain'],
                    'source' => $row['source'],
                ],
                array_merge($row, [
                    'verified_at' => now(),
                    'expires_at' => now()->addYear(),
                ])
            );
        }
    }
}
