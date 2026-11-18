<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationDataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'description',
        'total_rows',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
        ];
    }

    public function rows(): HasMany
    {
        return $this->hasMany(EvaluationDatasetRow::class, 'dataset_id');
    }

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
