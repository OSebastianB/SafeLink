<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightChangeLog extends Model
{
    protected $fillable = [
        'user_id',
        'criterion_id',
        'criterion_code',
        'old_weight',
        'new_weight',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_weight' => 'float',
            'new_weight' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }
}
