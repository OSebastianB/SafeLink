<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterion extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'code',
        'name',
        'description',
        'weight',
        'attribute',
        'max_score',
        'is_active',
        'is_core',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'max_score' => 'float',
            'is_active' => 'boolean',
            'is_core' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scales(): HasMany
    {
        return $this->hasMany(CriterionScale::class)->orderBy('sort_order');
    }

    public function activeScales(): HasMany
    {
        return $this->scales()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
