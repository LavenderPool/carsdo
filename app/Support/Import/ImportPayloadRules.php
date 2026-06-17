<?php

namespace App\Support\Import;

use Illuminate\Validation\Rule;

class ImportPayloadRules
{
    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public static function rootRules(): array
    {
        return [
            'brands' => ['required', 'array'],
            'cars' => ['required', 'array'],
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
            'year' => ['nullable', 'string', 'max:255'],
            'is_electric_car' => ['required', 'boolean'],
            'is_soon' => ['required', 'boolean'],
            'is_another_models' => ['required', 'boolean'],
            'start_price' => ['nullable', 'numeric'],
            'end_price' => ['nullable', 'numeric'],
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
            'groups' => ['sometimes', 'array'],
            'groups.*.name' => ['required', 'string', 'max:255'],
            'groups.*.order' => ['nullable', 'integer', 'min:0'],
            'groups.*.items' => ['sometimes', 'array'],
            'groups.*.items.*.price' => ['nullable', 'numeric'],
            'groups.*.items.*.engine_type' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.engine_capacity' => ['nullable', 'numeric'],
            'groups.*.items.*.horsepower' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.transmission' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.drive_type' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.fuel_city' => ['nullable', 'numeric'],
            'groups.*.items.*.fuel_highway' => ['nullable', 'numeric'],
            'groups.*.items.*.fuel_combined' => ['nullable', 'numeric'],
            'groups.*.items.*.acceleration' => ['nullable', 'numeric'],
            'groups.*.items.*.speed' => ['nullable', 'integer', 'min:0'],
            'groups.*.equipment' => ['sometimes', 'array'],
            'groups.*.equipment.*.name' => ['required', 'string', 'max:255'],
            'groups.*.equipment.*.items' => ['sometimes', 'array'],
            'groups.*.equipment.*.items.*.value' => ['nullable', 'string'],
            'groups.*.equipment.*.items.*.is_extension' => ['nullable', 'boolean'],
            'groups.*.equipment.*.items.*.price' => ['nullable', 'numeric'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'cars.required' => 'Передайте массив машин для импорта.',
            'slug.required' => 'У каждой машины должен быть slug.',
            'brand_slug.required' => 'У каждой машины должен быть brand_slug.',
        ];
    }
}
