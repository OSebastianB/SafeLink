<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCriterionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $criterionId = $this->route('criterion')?->id;

        return [
            'code' => ['required', 'string', 'max:10', Rule::unique('criteria', 'code')->ignore($criterionId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'weight' => ['required', 'numeric', 'min:0', 'max:1'],
            'attribute' => ['nullable', Rule::in(['benefit', 'cost'])],
            'max_score' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
