<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriterionScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'criterion_id',
        'condition_name',
        'condition_description',
        'score',
        'rule_configuration',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'rule_configuration' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }
}
