<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreatIntelligence extends Model
{
    use HasFactory;

    protected $table = 'threat_intelligence';

    public const STATUS_MALICIOUS = 'malicious';
    public const STATUS_PHISHING = 'phishing';
    public const STATUS_SUSPICIOUS = 'suspicious';
    public const STATUS_NO_MATCH = 'no_match';
    public const STATUS_UNKNOWN = 'unknown';

    protected $fillable = [
        'domain',
        'url_hash',
        'status',
        'threat_type',
        'source',
        'verified_at',
        'expires_at',
        'notes',
        'is_synthetic',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_synthetic' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isConfirmedThreat(): bool
    {
        return in_array($this->status, [
            self::STATUS_MALICIOUS,
            self::STATUS_PHISHING,
        ], true);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
