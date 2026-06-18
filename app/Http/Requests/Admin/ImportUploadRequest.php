<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'extensions:json', 'max:102400'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Выберите JSON-файл для импорта.',
            'file.file' => 'Переданный файл некорректен.',
            'file.extensions' => 'Допустимы только JSON-файлы.',
            'file.max' => 'Размер JSON-файла не должен превышать 100 МБ.',
        ];
    }
}
