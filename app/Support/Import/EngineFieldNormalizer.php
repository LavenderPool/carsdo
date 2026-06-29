<?php

namespace App\Support\Import;

use Illuminate\Support\Facades\Log;

class EngineFieldNormalizer
{
    private const CYRILLIC_TO_LATIN = [
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'e',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
    ];

    private const KNOWN_KEY_MAP = [
        'brand_slug' => 'brand_slug',
        'engine_slug' => 'engine_slug',
        'engine_name' => 'engine_name',
        'engine_url' => 'engine_url',
        'Тип двигателя' => 'engine_type',
        'Объем двигателя, куб.см' => 'displacement_cc',
        'Объем двигателя, см3' => 'displacement_cc',
        'Максимальная мощность, л.с.' => 'max_horsepower',
        'Максимальная мощность, л.с. (кВт) при об./мин.' => 'max_power_output_at_rpm',
        'Максимальный крутящий момент, Нм (кгм) при об./мин.' => 'max_torque_at_rpm',
        'Количество клапанов на цилиндр' => 'valves_per_cylinder',
        'Степень сжатия' => 'compression_ratio',
        'Диаметр цилиндра, мм' => 'cylinder_bore_mm',
        'Ход поршня, мм' => 'piston_stroke_mm',
        'Привод клапанов' => 'valvetrain',
        'Марка рекомендуемого топлива' => 'recommended_fuel_type',
        'Расход топлива, л/100 км' => 'fuel_consumption_l_per_100_km',
        'Выброс CO2, г/км' => 'co2_emissions_g_per_km',
        'Система старт-стоп' => 'has_start_stop_system',
        'Дополнительная информация о двигателе' => 'engine_notes',
        'text' => 'page_text',
    ];

    private const PREFERRED_STRING_KEYS = [
        'max_horsepower',
    ];

    private const ALLOWED_KEYS = [
        'brand_slug',
        'engine_slug',
        'engine_name',
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
        'has_start_stop_system',
        'engine_notes',
        'page_text',
    ];

    private const STRING_FIELD_LIMITS = [
        'brand_slug' => 255,
        'engine_slug' => 255,
        'engine_name' => 255,
        'engine_url' => 255,
        'engine_type' => 255,
        'displacement_cc' => 255,
        'max_horsepower' => 255,
        'max_power_output_at_rpm' => 255,
        'max_torque_at_rpm' => 255,
        'valves_per_cylinder' => 255,
        'compression_ratio' => 255,
        'cylinder_bore_mm' => 255,
        'piston_stroke_mm' => 255,
        'valvetrain' => 255,
        'recommended_fuel_type' => 255,
        'fuel_consumption_l_per_100_km' => 255,
        'co2_emissions_g_per_km' => 255,
    ];

    /**
     * @param  array<string, mixed>  $payload
     * @return array{attributes: array<string, mixed>, unknown_keys: array<int, array{source: string, normalized: string}>}
     */
    public function normalizeRecord(array $payload): array
    {
        $attributes = [];
        $unknownKeys = [];

        foreach ($payload as $sourceKey => $value) {
            $sourceLabel = (string) $sourceKey;
            $normalizedKey = $this->normalizeKey($sourceLabel);

            if (!in_array($normalizedKey, self::ALLOWED_KEYS, true)) {
                $unknownKeys[] = [
                    'source' => $sourceLabel,
                    'normalized' => $normalizedKey,
                ];

                continue;
            }

            $attributes[$normalizedKey] = $this->normalizeValue($normalizedKey, $value);
        }

        return [
            'attributes' => $attributes,
            'unknown_keys' => $unknownKeys,
        ];
    }

    private function normalizeKey(string $sourceLabel): string
    {
        if (array_key_exists($sourceLabel, self::KNOWN_KEY_MAP)) {
            return self::KNOWN_KEY_MAP[$sourceLabel];
        }

        $normalized = $this->transliterate(mb_strtolower($sourceLabel));
        $normalized = str_replace('л.с.', 'ls', $normalized);
        $normalized = str_replace('см.куб.', 'sm_kub', $normalized);
        $normalized = str_replace('куб.см', 'kub_sm', $normalized);
        $normalized = str_replace('кгм', 'kgm', $normalized);
        $normalized = str_replace('мм', 'mm', $normalized);
        $normalized = str_replace('квт', 'kw', $normalized);
        $normalized = preg_replace('/[^a-z0-9]+/u', '_', $normalized) ?? '';
        $normalized = preg_replace('/_+/u', '_', $normalized) ?? '';
        $normalized = trim($normalized, '_');

        return $normalized !== '' ? $normalized : 'unknown_field';
    }

    private function transliterate(string $value): string
    {
        $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $result = '';

        foreach ($characters as $character) {
            $result .= self::CYRILLIC_TO_LATIN[$character] ?? $character;
        }

        return $result;
    }

    private function normalizeValue(string $normalizedKey, mixed $value): string|bool|null
    {
        if ($value === null) {
            return null;
        }

        if ($normalizedKey === 'has_start_stop_system') {
            return $this->normalizeBoolean($value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (!is_string($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        if (in_array($normalizedKey, self::PREFERRED_STRING_KEYS, true)) {
            return $this->applyStringLimit($normalizedKey, $normalized);
        }

        return $this->applyStringLimit($normalizedKey, $normalized);
    }

    private function normalizeBoolean(mixed $value): ?bool
    {
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
            'да', 'true', '1', 'yes' => true,
            'нет', 'false', '0', 'no' => false,
            default => filter_var($normalized, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
        };
    }

    private function applyStringLimit(string $normalizedKey, string $value): string
    {
        $limit = self::STRING_FIELD_LIMITS[$normalizedKey] ?? null;

        if ($limit === null || mb_strlen($value) <= $limit) {
            return $value;
        }

        $truncated = mb_substr($value, 0, $limit);

        Log::warning('engine_import.value_truncated', [
            'field' => $normalizedKey,
            'original_length' => mb_strlen($value),
            'limit' => $limit,
            'preview' => mb_substr($value, 0, 120),
        ]);

        return $truncated;
    }
}
