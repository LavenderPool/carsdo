<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCarCatalogRequest extends FormRequest
{
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
            'description' => $this->normalizeNullableString('description'),
            'is_published' => $this->boolean('is_published'),
            'sort_order' => $this->normalizeNullableInt('sort_order'),
            'price_min' => $this->normalizeNullableInt('price_min'),
            'price_max' => $this->normalizeNullableInt('price_max'),
            'year_from' => $this->normalizeNullableInt('year_from'),
            'year_to' => $this->normalizeNullableInt('year_to'),
            'is_electric_car' => $this->normalizeNullableBoolean('is_electric_car'),
            'brand_ids' => $this->normalizeIntArray('brand_ids'),
            'drive_types' => $this->normalizeStringArray('drive_types'),
            'engine_types' => $this->normalizeStringArray('engine_types'),
            'manual_cars' => $this->normalizeManualCars(),
            'seo_title' => $this->normalizeNullableString('seo_title'),
            'seo_description' => $this->normalizeNullableString('seo_description'),
            'seo_h1' => $this->normalizeNullableString('seo_h1'),
            'seo_og_image' => $this->normalizeNullableString('seo_og_image'),
            'seo_canonical_url' => $this->normalizeNullableString('seo_canonical_url'),
            'seo_robots' => $this->normalizeNullableString('seo_robots'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('car_catalogs', 'slug')],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0', 'gte:price_min'],
            'year_from' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'year_to' => ['nullable', 'integer', 'min:1900', 'max:2100', 'gte:year_from'],
            'is_electric_car' => ['nullable', 'boolean'],
            'brand_ids' => ['array'],
            'brand_ids.*' => ['integer', 'distinct', Rule::exists('brands', 'id')],
            'drive_types' => ['array'],
            'drive_types.*' => ['string', 'distinct', 'max:255'],
            'engine_types' => ['array'],
            'engine_types.*' => ['string', 'distinct', 'max:255'],
            'manual_cars' => ['array'],
            'manual_cars.*.car_id' => ['required', 'integer', 'distinct', Rule::exists('cars', 'id')],
            'manual_cars.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:5000'],
            'seo_h1' => ['nullable', 'string', 'max:255'],
            'seo_og_image' => ['nullable', 'string', 'max:2048'],
            'seo_canonical_url' => ['nullable', 'string', 'max:2048'],
            'seo_robots' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<int, array{car_id: int|null, sort_order: int}>
     */
    private function normalizeManualCars(): array
    {
        $items = $this->input('manual_cars');

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): array => [
                'car_id' => is_numeric($item['car_id'] ?? null) ? (int) $item['car_id'] : null,
                'sort_order' => is_numeric($item['sort_order'] ?? null) ? (int) $item['sort_order'] : 0,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function normalizeIntArray(string $key): array
    {
        $value = $this->input($key);

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $item): bool => is_numeric($item))
            ->map(fn (mixed $item): int => (int) $item)
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function normalizeStringArray(string $key): array
    {
        $value = $this->input($key);

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(fn (string $item): string => trim($item))
            ->values()
            ->all();
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = $this->input($key);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private function normalizeNullableInt(string $key): ?int
    {
        $value = $this->input($key);

        return is_numeric($value) ? (int) $value : null;
    }

    private function normalizeNullableBoolean(string $key): ?bool
    {
        if (! $this->has($key)) {
            return null;
        }

        $value = $this->input($key);

        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            if (in_array($value, ['1', 'true'], true)) {
                return true;
            }

            if (in_array($value, ['0', 'false'], true)) {
                return false;
            }
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return null;
    }
}
