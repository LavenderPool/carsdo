<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreEngineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = (string) $this->input('name', '');
        $slug = (string) $this->input('slug', '');

        $this->merge([
            'brand_id' => $this->input('brand_id'),
            'name' => trim($name),
            'slug' => trim($slug) !== '' ? Str::slug($slug) : Str::slug($name),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brandId = (int) $this->input('brand_id');

        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('engines', 'slug')->where(
                    fn ($query) => $query->where('brand_id', $brandId)
                ),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.required' => 'Выберите бренд двигателя.',
            'brand_id.exists' => 'Выбранный бренд не найден.',
            'name.required' => 'Укажите название двигателя.',
            'name.max' => 'Название двигателя не должно превышать 255 символов.',
            'slug.required' => 'Укажите идентификатор двигателя.',
            'slug.max' => 'Идентификатор двигателя не должен превышать 255 символов.',
            'slug.unique' => 'У этого бренда уже есть двигатель с таким идентификатором.',
        ];
    }
}
