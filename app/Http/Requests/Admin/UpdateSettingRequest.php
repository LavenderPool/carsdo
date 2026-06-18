<?php

namespace App\Http\Requests\Admin;

use App\Support\Seo\AdminSeoFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [
            'brand_name' => trim((string) $this->input('brand_name', '')),
        ];

        foreach (AdminSeoFields::settingFields() as $field) {
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
        $rules = [
            'brand_name' => ['required', 'string', 'max:255'],
            'favicon' => [
                'nullable',
                File::types(['ico', 'png', 'svg'])
                    ->max(2048),
            ],
        ];

        foreach (AdminSeoFields::settingFields() as $field) {
            $rules[$field] = $this->seoFieldRules($field);
        }

        return $rules;
    }

    /**
     * Get custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge([
            'brand_name.required' => 'Укажите название бренда.',
            'brand_name.max' => 'Название бренда не должно превышать 255 символов.',
            'favicon.max' => 'Размер favicon не должен превышать 2 МБ.',
            'favicon.extensions' => 'Разрешены только файлы .ico, .png или .svg.',
            'favicon.mimes' => 'Разрешены только файлы .ico, .png или .svg.',
        ], $this->seoMessages());
    }

    /**
     * @return array<int, ValidationRule|string>
     */
    private function seoFieldRules(string $field): array
    {
        return match (true) {
            str_ends_with($field, '_description') => ['nullable', 'string', 'max:5000'],
            str_ends_with($field, '_canonical_url'), str_ends_with($field, '_og_image') => ['nullable', 'string', 'max:2048'],
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

    /**
     * @return array<string, string>
     */
    private function seoMessages(): array
    {
        return [
            '*.max' => 'Значение поля SEO превышает допустимую длину.',
        ];
    }
}
