<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCarRequest extends FormRequest
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
        $name = trim((string) $this->input('name', ''));
        $slug = trim((string) $this->input('slug', ''));

        $this->merge([
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'year' => $this->filled('year') ? trim((string) $this->input('year')) : null,
            'official_site' => $this->filled('official_site') ? trim((string) $this->input('official_site')) : null,
            'cover_path' => $this->filled('cover_path') ? trim((string) $this->input('cover_path')) : null,
            'is_electric_car' => $this->boolean('is_electric_car'),
            'is_soon' => $this->boolean('is_soon'),
            'is_another_models' => $this->boolean('is_another_models'),
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
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('cars', 'slug')],
            'year' => ['nullable', 'string', 'max:255'],
            'cover_path' => ['nullable', 'string', 'max:255'],
            'start_price' => ['nullable', 'integer', 'min:0'],
            'end_price' => ['nullable', 'integer', 'min:0'],
            'official_site' => ['nullable', 'url', 'max:65535'],
            'is_electric_car' => ['boolean'],
            'is_soon' => ['boolean'],
            'is_another_models' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.required' => 'Выберите бренд.',
            'brand_id.exists' => 'Выбранный бренд не найден.',
            'name.required' => 'Укажите название автомобиля.',
            'slug.required' => 'Укажите идентификатор автомобиля.',
            'slug.unique' => 'Автомобиль с таким идентификатором уже существует.',
            'official_site.url' => 'Официальный сайт должен быть корректной ссылкой.',
        ];
    }
}
