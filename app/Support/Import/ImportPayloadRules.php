<?php

namespace App\Support\Import;

use Closure;
use Illuminate\Validation\Rule;

class ImportPayloadRules
{
    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public static function rootRules(): array
    {
        return [
            'cities' => ['sometimes', 'array'],
            'brands' => ['sometimes', 'array'],
            'cars' => ['required', 'array'],
        ];
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public static function cityRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public static function brandRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'leave_from_russian' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public static function carRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'brand_slug' => ['required', 'string', 'max:255'],
            'versions' => ['sometimes', 'array'],
            'versions.*' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:255'],
            'is_electric_car' => ['required', 'boolean'],
            'is_soon' => ['required', 'boolean'],
            'is_another_models' => ['required', 'boolean'],
            'start_price' => ['nullable', 'numeric'],
            'end_price' => ['nullable', 'numeric'],
            'cover_path' => ['nullable', 'string', 'max:255'],
            'crash_test' => ['nullable', 'array'],
            'crash_test.year' => ['nullable', 'integer', 'min:1900'],
            'crash_test.rating' => ['nullable', 'numeric'],
            'crash_test.video_path' => ['nullable', 'string'],
            'test_drives' => ['sometimes', 'array'],
            'test_drives.*.author' => ['required', 'string', 'max:255'],
            'test_drives.*.path' => ['required', 'string'],
            'reviews' => ['sometimes', 'array'],
            'reviews.*.type' => ['required', 'string', Rule::in(['good', 'bad'])],
            'reviews.*.value' => ['required', 'string'],
            'photo_groups' => ['sometimes', 'array'],
            'photo_groups.*.name' => ['required', 'string', 'max:255'],
            'photo_groups.*.photo_list' => ['sometimes', 'array'],
            'photo_groups.*.photo_list.*' => ['required', 'string', 'max:255'],
            'dealers' => ['sometimes', 'array'],
            'dealers.*.name' => ['required', 'string', 'max:255'],
            'dealers.*.city_slug' => ['required', 'string', 'max:255'],
            'dealers.*.is_official_deler' => ['nullable', 'boolean'],
            'dealers.*.is_official' => ['nullable', 'boolean'],
            'dealers.*.address' => ['nullable', 'string', 'max:255'],
            'dealers.*.phone' => ['nullable', 'string', 'max:255'],
            'dealers.*.url' => ['nullable', 'string', 'max:255'],
            'dealers.*.website' => ['nullable', 'string', 'max:255'],
            'groups' => ['sometimes', 'array'],
            'groups.*.name' => ['required', 'string', 'max:255'],
            'groups.*.order' => ['nullable', 'integer', 'min:0'],
            'groups.*.items' => ['sometimes', 'array'],
            'groups.*.items.*.local_id' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.have_page' => ['nullable', 'boolean'],
            'groups.*.items.*.price' => ['nullable', 'numeric'],
            'groups.*.items.*.engine_slug' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.engine_type' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.engine_capacity' => ['nullable', 'numeric'],
            'groups.*.items.*.horsepower' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.transmission' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.drive_type' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.fuel_city' => ['nullable', 'numeric'],
            'groups.*.items.*.fuel_highway' => ['nullable', 'numeric'],
            'groups.*.items.*.fuel_combined' => ['nullable', 'numeric'],
            'groups.*.items.*.acceleration' => [
                'nullable',
                'numeric',
                'min:0',
                static function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $numericValue = (float) $value;

                    if ($numericValue > 999.9) {
                        $numericValue /= 1000;
                    }

                    if ($numericValue > 999.9) {
                        $fail("Поле {$attribute} содержит слишком большое значение acceleration.");
                    }
                },
            ],
            'groups.*.items.*.speed' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.equipment' => ['sometimes', 'array'],
            'groups.*.items.*.equipment.*.name' => ['required', 'string', 'max:255'],
            'groups.*.items.*.equipment.*.items' => ['sometimes', 'array'],
            'groups.*.items.*.equipment.*.items.*.value' => ['nullable', 'string'],
            'groups.*.items.*.equipment.*.items.*.is_extension' => ['nullable', 'boolean'],
            'groups.*.items.*.equipment.*.items.*.price' => ['nullable', 'numeric'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'cars.required' => 'Передайте массив машин для импорта.',
            'cities.array' => 'Города должны быть переданы массивом.',
            'slug.required' => 'У каждой машины должен быть slug.',
            'brand_slug.required' => 'У каждой машины должен быть brand_slug.',
        ];
    }
}
