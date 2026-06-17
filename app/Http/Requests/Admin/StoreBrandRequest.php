<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
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
        $name = (string) $this->input('name', '');
        $slug = (string) $this->input('slug', '');
        $leaveFromRussian = $this->boolean('leave_from_russian');

        $this->merge([
            'name' => trim($name),
            'slug' => trim($slug) !== '' ? Str::slug($slug) : Str::slug($name),
            'leave_from_russian' => $leaveFromRussian,
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('brands', 'slug')],
            'leave_from_russian' => ['boolean'],
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
            'name.required' => 'Укажите название бренда.',
            'name.max' => 'Название бренда не должно превышать 255 символов.',
            'slug.required' => 'Укажите идентификатор бренда.',
            'slug.max' => 'Идентификатор бренда не должен превышать 255 символов.',
            'slug.unique' => 'Бренд с таким идентификатором уже существует.',
        ];
    }
}
