<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'normalized_url',
        'domain',
        'protocol',
        'security_score',
        'risk_index',
        'risk_category',
        'data_coverage_percentage',
        'threat_match_status',
        'warning_message',
        'explanation',
        'url_features',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'security_score' => 'float',
            'risk_index' => 'float',
            'data_coverage_percentage' => 'float',
            'url_features' => 'array',
            'scanned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function criterionResults(): HasMany
    {
        return $this->hasMany(ScanCriterionResult::class);
    }

    public function isLowRisk(): bool
    {
        return $this->risk_category === 'low';
    }

    public function isMediumRisk(): bool
    {
        return $this->risk_category === 'medium';
    }

    public function isHighRisk(): bool
    {
        return $this->risk_category === 'high';
    }

    public function riskLabel(): string
    {
        return match ($this->risk_category) {
            'low' => 'Risiko Rendah',
            'medium' => 'Risiko Sedang',
            'high' => 'Risiko Tinggi',
            default => 'Tidak diketahui',
        };
    }

    public function riskBadgeClass(): string
    {
        return match ($this->risk_category) {
            'low' => 'bg-success',
            'medium' => 'bg-warning text-dark',
            'high' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
