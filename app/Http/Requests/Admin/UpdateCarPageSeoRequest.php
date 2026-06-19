<?php

namespace App\Http\Requests\Admin;

use App\Support\Seo\AdminSeoFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCarPageSeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (AdminSeoFields::BASE_KEYS as $field) {
            $data[$field] = $this->normalizeSeoField($field);
        }

        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (AdminSeoFields::BASE_KEYS as $field) {
            $rules[$field] = $this->seoFieldRules($field);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.max' => 'Значение поля SEO превышает допустимую длину.',
        ];
    }

    /**
     * @return array<int, ValidationRule|string>
     */
    private function seoFieldRules(string $field): array
    {
        return match (true) {
            $field === 'description' => ['nullable', 'string', 'max:5000'],
            in_array($field, ['canonical_url', 'og_image'], true) => ['nullable', 'string', 'max:2048'],
            default => ['nullable', 'string', 'max:255'],
        };
    }

    private function normalizeSeoField(string $field): ?string
    {
        $value = $this->input($field);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
