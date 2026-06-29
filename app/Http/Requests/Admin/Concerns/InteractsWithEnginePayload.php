<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Models\Engine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** @mixin FormRequest */
trait InteractsWithEnginePayload
{
    /**
     * @var array<int, string>
     */
    private const STRING_FIELDS = [
        'engine_url',
        'engine_type',
        'displacement_cc',
        'max_horsepower',
        'max_power_output_at_rpm',
        'max_torque_at_rpm',
        'valves_per_cylinder',
        'compression_ratio',
        'cylinder_bore_mm',
        'piston_stroke_mm',
        'valvetrain',
        'recommended_fuel_type',
        'fuel_consumption_l_per_100_km',
        'co2_emissions_g_per_km',
        'engine_notes',
        'page_text',
    ];

    protected function prepareEnginePayloadForValidation(): void
    {
        $name = (string) $this->input('name', '');
        $slug = (string) $this->input('slug', '');
        $payload = [
            'brand_id' => $this->input('brand_id'),
            'name' => trim($name),
            'slug' => trim($slug) !== '' ? str($slug)->slug()->value() : str($name)->slug()->value(),
            'has_start_stop_system' => $this->normalizeNullableBoolean($this->input('has_start_stop_system')),
        ];

        foreach (self::STRING_FIELDS as $field) {
            $payload[$field] = $this->normalizeNullableString($this->input($field));
        }

        $this->merge($payload);
    }

    /**
     * @return array<string, mixed>
     */
    protected function engineValidationRules(?Engine $engine = null): array
    {
        $brandId = (int) $this->input('brand_id');
        $slugRule = Rule::unique('engines', 'slug')
            ->where(fn ($query) => $query->where('brand_id', $brandId));

        if ($engine !== null) {
            $slugRule->ignore($engine->id);
        }

        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'engine_url' => ['nullable', 'string', 'max:255'],
            'engine_type' => ['nullable', 'string', 'max:255'],
            'displacement_cc' => ['nullable', 'string', 'max:255'],
            'max_horsepower' => ['nullable', 'string', 'max:255'],
            'max_power_output_at_rpm' => ['nullable', 'string', 'max:255'],
            'max_torque_at_rpm' => ['nullable', 'string', 'max:255'],
            'valves_per_cylinder' => ['nullable', 'string', 'max:255'],
            'compression_ratio' => ['nullable', 'string', 'max:255'],
            'cylinder_bore_mm' => ['nullable', 'string', 'max:255'],
            'piston_stroke_mm' => ['nullable', 'string', 'max:255'],
            'valvetrain' => ['nullable', 'string', 'max:255'],
            'recommended_fuel_type' => ['nullable', 'string', 'max:255'],
            'fuel_consumption_l_per_100_km' => ['nullable', 'string', 'max:255'],
            'co2_emissions_g_per_km' => ['nullable', 'string', 'max:255'],
            'has_start_stop_system' => ['nullable', 'boolean'],
            'engine_notes' => ['nullable', 'string'],
            'page_text' => ['nullable', 'string'],
        ];
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeNullableBoolean(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (bool) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $normalized = mb_strtolower(trim($value));

        if ($normalized === '') {
            return null;
        }

        return match ($normalized) {
            '1', 'true', 'on', 'yes', 'да' => true,
            '0', 'false', 'off', 'no', 'нет' => false,
            default => filter_var($normalized, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
        };
    }
}
