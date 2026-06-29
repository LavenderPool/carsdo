<?php

namespace App\Services\Import;

use App\Models\Brand;
use App\Models\Engine;
use App\Support\Import\EngineFieldNormalizer;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EngineImportService
{
    /**
     * @var array<int, string>
     */
    private const ATTRIBUTE_KEYS = [
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

    public function __construct(
        private readonly EngineFieldNormalizer $fieldNormalizer,
    ) {
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  null|callable(array{new: int, updated: int, unchanged: int, processed: int, processed_engines: int}): void  $afterEngineProcessed
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_engines: int}
     */
    public function import(array $payload, ?callable $afterEngineProcessed = null): array
    {
        $stats = $this->freshStats();
        $brandsBySlug = $this->loadBrandsBySlug($payload);
        $unknownKeys = [];

        foreach ($payload as $index => $record) {
            if (!is_array($record)) {
                $humanIndex = $index + 1;
                throw new RuntimeException("Ошибка валидации двигателя #{$humanIndex}: запись должна быть объектом JSON.");
            }

            $normalizedRecord = $this->fieldNormalizer->normalizeRecord($record);
            $attributes = $normalizedRecord['attributes'];

            foreach ($normalizedRecord['unknown_keys'] as $unknownKey) {
                $unknownKeys[$unknownKey['source']] = $unknownKey['normalized'];
            }

            $brandSlug = $this->normalizeString($attributes['brand_slug'] ?? null);
            $engineSlug = $this->normalizeString($attributes['engine_slug'] ?? null);
            $engineName = $this->normalizeString($attributes['engine_name'] ?? null);

            $humanIndex = $index + 1;

            if ($brandSlug === null) {
                throw new RuntimeException("Ошибка валидации двигателя #{$humanIndex}: отсутствует brand_slug.");
            }

            if ($engineSlug === null) {
                throw new RuntimeException("Ошибка валидации двигателя #{$humanIndex}: отсутствует engine_slug.");
            }

            if ($engineName === null) {
                throw new RuntimeException("Ошибка валидации двигателя #{$humanIndex}: отсутствует engine_name.");
            }

            $stats['processed_engines']++;

            $brand = $brandsBySlug[$brandSlug] ?? null;

            if (!$brand instanceof Brand) {
                if ($afterEngineProcessed !== null) {
                    $afterEngineProcessed($stats);
                }

                continue;
            }

            $engineAttributes = [
                'brand_id' => $brand->id,
                'name' => $engineName,
                'slug' => $engineSlug,
            ];

            foreach (self::ATTRIBUTE_KEYS as $attributeKey) {
                $engineAttributes[$attributeKey] = $attributes[$attributeKey] ?? null;
            }

            $engine = Engine::query()
                ->where('brand_id', $brand->id)
                ->where('slug', $engineSlug)
                ->first();

            $this->syncModel($engine, $engineAttributes, $stats);

            if ($afterEngineProcessed !== null) {
                $afterEngineProcessed($stats);
            }
        }

        if ($unknownKeys !== []) {
            Log::warning('engine_import.unknown_keys_detected', [
                'keys' => array_map(
                    fn (string $source, string $normalized): array => [
                        'source' => $source,
                        'normalized' => $normalized,
                    ],
                    array_keys($unknownKeys),
                    array_values($unknownKeys),
                ),
            ]);
        }

        return $stats;
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @return array<string, Brand>
     */
    private function loadBrandsBySlug(array $payload): array
    {
        $brandSlugs = [];

        foreach ($payload as $record) {
            if (!is_array($record)) {
                continue;
            }

            $normalizedRecord = $this->fieldNormalizer->normalizeRecord($record);
            $brandSlug = $this->normalizeString($normalizedRecord['attributes']['brand_slug'] ?? null);

            if ($brandSlug !== null) {
                $brandSlugs[] = $brandSlug;
            }
        }

        return Brand::query()
            ->whereIn('slug', array_values(array_unique($brandSlugs)), 'and', false)
            ->get()
            ->keyBy('slug')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_engines: int}  $stats
     */
    private function syncModel(?Engine $engine, array $attributes, array &$stats): void
    {
        if ($engine === null) {
            Engine::query()->create($attributes);
            $this->bump($stats, 'new');

            return;
        }

        $engine->fill($attributes);

        if ($engine->isDirty()) {
            $engine->save();
            $this->bump($stats, 'updated');

            return;
        }

        $this->bump($stats, 'unchanged');
    }

    /**
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_engines: int}
     */
    private function freshStats(): array
    {
        return [
            'new' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'processed' => 0,
            'processed_engines' => 0,
        ];
    }

    /**
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_engines: int}  $stats
     */
    private function bump(array &$stats, string $key): void
    {
        $stats[$key]++;
        $stats['processed']++;
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
