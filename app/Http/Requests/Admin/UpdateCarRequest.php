<?php

namespace App\Http\Requests\Admin;

use App\Models\Car;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
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

        $data = [
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'year' => $this->filled('year') ? trim((string) $this->input('year')) : null,
            'official_site' => $this->filled('official_site') ? trim((string) $this->input('official_site')) : null,
            'cover_path' => $this->filled('cover_path') ? trim((string) $this->input('cover_path')) : null,
            'is_electric_car' => $this->boolean('is_electric_car'),
            'is_soon' => $this->boolean('is_soon'),
            'is_another_models' => $this->boolean('is_another_models'),
        ];

        foreach (AdminSeoFields::carFields() as $field) {
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
        /** @var Car $car */
        $car = $this->route('car');

        $rules = [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('cars', 'slug')->ignore($car->id)],
            'year' => ['nullable', 'string', 'max:255'],
            'cover_path' => ['nullable', 'string', 'max:255'],
            'start_price' => ['nullable', 'integer', 'min:0'],
            'end_price' => ['nullable', 'integer', 'min:0'],
            'official_site' => ['nullable', 'url', 'max:65535'],
            'is_electric_car' => ['boolean'],
            'is_soon' => ['boolean'],
            'is_another_models' => ['boolean'],
        ];

        foreach (AdminSeoFields::carFields() as $field) {
            $rules[$field] = $this->seoFieldRules($field);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge([
            'brand_id.required' => 'Выберите бренд.',
            'brand_id.exists' => 'Выбранный бренд не найден.',
            'name.required' => 'Укажите название автомобиля.',
            'slug.required' => 'Укажите идентификатор автомобиля.',
            'slug.unique' => 'Автомобиль с таким идентификатором уже существует.',
            'official_site.url' => 'Официальный сайт должен быть корректной ссылкой.',
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
        $messages = [];

        foreach (AdminSeoFields::CAR_PAGE_PREFIXES as $prefix) {
            $messages["{$prefix}_title.max"] = 'SEO title не должен превышать 255 символов.';
            $messages["{$prefix}_h1.max"] = 'SEO H1 не должен превышать 255 символов.';
            $messages["{$prefix}_canonical_url.max"] = 'SEO canonical не должен превышать 2048 символов.';
            $messages["{$prefix}_og_image.max"] = 'SEO og:image не должен превышать 2048 символов.';
            $messages["{$prefix}_robots.max"] = 'SEO robots не должен превышать 255 символов.';
        }

        return $messages;
    }
}
