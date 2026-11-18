<?php

namespace App\Http\Requests;

use App\Services\Url\UrlParserService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ScanUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:'.UrlParserService::MAX_URL_LENGTH],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'URL wajib diisi.',
            'url.max' => 'URL melebihi batas panjang maksimum.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $url = (string) $this->input('url', '');
            if ($url === '') {
                return;
            }

            try {
                app(UrlParserService::class)->parse($url);
            } catch (\InvalidArgumentException $e) {
                $validator->errors()->add('url', $e->getMessage());
            }
        });
    }
}
