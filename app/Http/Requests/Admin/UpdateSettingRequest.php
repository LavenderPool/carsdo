<?php

namespace App\Http\Requests\Admin;

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
        $this->merge([
            'brand_name' => trim((string) $this->input('brand_name', '')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand_name' => ['required', 'string', 'max:255'],
            'favicon' => [
                'nullable',
                File::types(['ico', 'png', 'svg'])
                    ->max(2048),
            ],
        ];
    }

    /**
     * Get custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_name.required' => 'Укажите название бренда.',
            'brand_name.max' => 'Название бренда не должно превышать 255 символов.',
            'favicon.max' => 'Размер favicon не должен превышать 2 МБ.',
            'favicon.extensions' => 'Разрешены только файлы .ico, .png или .svg.',
            'favicon.mimes' => 'Разрешены только файлы .ico, .png или .svg.',
        ];
    }
}
