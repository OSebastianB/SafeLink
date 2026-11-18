<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDatasetRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'url',
        'ground_truth_label',
        'source_reference',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(EvaluationDataset::class, 'dataset_id');
    }
}
