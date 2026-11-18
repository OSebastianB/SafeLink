<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanCriterionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'criterion_id',
        'criterion_code_snapshot',
        'criterion_name_snapshot',
        'criterion_weight_snapshot',
        'raw_value',
        'score',
        'normalized_value',
        'weighted_contribution',
        'evidence',
        'data_source',
        'data_status',
    ];

    protected function casts(): array
    {
        return [
            'criterion_weight_snapshot' => 'float',
            'score' => 'float',
            'normalized_value' => 'float',
            'weighted_contribution' => 'float',
        ];
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    public function isUnavailable(): bool
    {
        return $this->data_status === 'unavailable';
    }
}
