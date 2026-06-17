<?php

namespace App\Services\Import;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CarImportService
{
    /**
     * @param  array{
     *     brands?: array<int, array<string, mixed>>,
     *     cars?: array<int, array<string, mixed>>
     * }  $payload
     * @param  null|callable(array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}): void  $afterCarProcessed
     * @param  null|callable(array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}): void  $afterBrandsProcessed
     * @param  null|callable(): bool  $shouldStop
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    public function import(
        array $payload,
        ?callable $afterCarProcessed = null,
        ?callable $afterBrandsProcessed = null,
        ?callable $shouldStop = null,
    ): array
    {
        $stats = $this->freshStats();
        $stats = $this->importBrands($payload['brands'] ?? [], $stats);

        if ($afterBrandsProcessed !== null) {
            $afterBrandsProcessed($stats);
        }

        return $this->importCarsChunk(
            $payload['cars'] ?? [],
            $stats,
            $afterCarProcessed,
            $shouldStop,
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $brandsPayload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}|null  $stats
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    public function importBrands(array $brandsPayload, ?array $stats = null): array
    {
        $stats ??= $this->freshStats();

        foreach ($brandsPayload as $brandPayload) {
            $this->upsertBrand($brandPayload, $stats);
        }

        return $stats;
    }

    /**
     * @param  array<int, array<string, mixed>>  $carsPayload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     * @param  null|callable(array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}): void  $afterCarProcessed
     * @param  null|callable(): bool  $shouldStop
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    public function importCarsChunk(
        array $carsPayload,
        array $stats,
        ?callable $afterCarProcessed = null,
        ?callable $shouldStop = null,
    ): array {
        foreach ($carsPayload as $carPayload) {
            if ($shouldStop !== null && $shouldStop()) {
                break;
            }

            DB::transaction(function () use ($carPayload, &$stats): void {
                $this->upsertCar($carPayload, $stats);
            });

            $stats['processed_cars']++;

            if ($afterCarProcessed !== null) {
                $afterCarProcessed($stats);
            }
        }

        return $stats;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function upsertBrand(array $payload, array &$stats): Brand
    {
        $attributes = [
            'name' => $this->normalizeString($payload['name'] ?? null) ?? '',
            'slug' => $this->normalizeString($payload['slug'] ?? null) ?? '',
            'leave_from_russian' => $this->normalizeBoolean($payload['leave_from_russian'] ?? false),
        ];

        /** @var Brand|null $brand */
        $brand = Brand::withTrashed()
            ->where('slug', $attributes['slug'])
            ->first();

        if ($brand === null) {
            /** @var Brand $brand */
            $brand = Brand::query()->create($attributes);
            $this->bump($stats, 'new');

            return $brand;
        }

        $wasRestored = false;

        if ($brand->trashed()) {
            $brand->restore();
            $wasRestored = true;
        }

        $brand->fill($attributes);

        if ($brand->isDirty()) {
            $brand->save();
            $this->bump($stats, 'updated');

            return $brand;
        }

        $this->bump($stats, $wasRestored ? 'updated' : 'unchanged');

        return $brand;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function upsertCar(array $payload, array &$stats): Car
    {
        $brandSlug = $this->normalizeString($payload['brand_slug'] ?? null);
        $slug = $this->normalizeString($payload['slug'] ?? null);

        if ($brandSlug === null) {
            Log::error('import.car_failed', [
                'brand_slug' => null,
                'car_slug' => $slug,
                'reason' => 'missing_brand_slug',
            ]);
            throw new RuntimeException('Не указан brand_slug у импортируемой машины.');
        }

        if ($slug === null) {
            Log::error('import.car_failed', [
                'brand_slug' => $brandSlug,
                'car_slug' => null,
                'reason' => 'missing_car_slug',
            ]);
            throw new RuntimeException('Не указан slug у импортируемой машины.');
        }

        /** @var Brand|null $brand */
        $brand = Brand::query()
            ->where('slug', $brandSlug)
            ->first();

        if ($brand === null) {
            Log::error('import.car_failed', [
                'brand_slug' => $brandSlug,
                'car_slug' => $slug,
                'reason' => 'brand_not_found',
            ]);
            throw new RuntimeException("Бренд со slug \"{$brandSlug}\" не найден.");
        }

        $attributes = [
            'brand_id' => $brand->id,
            'name' => $this->normalizeString($payload['name'] ?? null) ?? '',
            'slug' => $slug,
            'year' => $this->normalizeString($payload['year'] ?? null),
            'is_electric_car' => $this->normalizeBoolean($payload['is_electric_car'] ?? false),
            'is_soon' => $this->normalizeBoolean($payload['is_soon'] ?? false),
            'is_another_models' => $this->normalizeBoolean($payload['is_another_models'] ?? false),
            'start_price' => $this->normalizeInteger($payload['start_price'] ?? null),
            'end_price' => $this->normalizeInteger($payload['end_price'] ?? null),
        ];

        /** @var Car|null $car */
        $car = Car::query()
            ->where('slug', $slug)
            ->first();

        /** @var Car $car */
        $car = $this->syncModel($car, Car::class, $attributes, $stats);

        $this->syncCrashTest($car, $payload['crash_test'] ?? null, $stats);
        $this->syncTestDrives($car, $payload['test_drives'] ?? [], $stats);

        if (array_key_exists('reviews', $payload) && is_array($payload['reviews'])) {
            $this->syncReviews($car, $payload['reviews'], $stats);
        }

        $this->syncConfigurationGroups($car, $payload['groups'] ?? [], $stats);

        return $car;
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncCrashTest(Car $car, ?array $payload, array &$stats): void
    {
        if ($payload === null) {
            return;
        }

        /** @var CarCrashTest|null $crashTest */
        $crashTest = $car->crashTest()->first();

        $this->syncModel($crashTest, CarCrashTest::class, [
            'car_id' => $car->id,
            'year' => $this->normalizeInteger($payload['year'] ?? null),
            'rating' => $this->normalizeInteger($payload['rating'] ?? null),
            'video_path' => $this->normalizeString($payload['video_path'] ?? null),
        ], $stats);
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncTestDrives(Car $car, array $payload, array &$stats): void
    {
        foreach ($payload as $testDriveIndex => $testDrivePayload) {
            $author = $this->normalizeString($testDrivePayload['author'] ?? null) ?? '';
            $videoPath = $this->normalizeString($testDrivePayload['path'] ?? null) ?? '';

            /** @var CarTestDrive|null $testDrive */
            $testDrive = $car->testDrives()
                ->where('import_index', $testDriveIndex)
                ->first();

            $this->syncModel($testDrive, CarTestDrive::class, [
                'car_id' => $car->id,
                'import_index' => $testDriveIndex,
                'author' => $author,
                'video_path' => $videoPath,
            ], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncReviews(Car $car, array $payload, array &$stats): void
    {
        foreach ($payload as $reviewIndex => $reviewPayload) {
            $type = $this->normalizeString($reviewPayload['type'] ?? null) ?? '';
            $value = $this->normalizeString($reviewPayload['value'] ?? null) ?? '';

            /** @var CarReview|null $review */
            $review = $car->reviews()
                ->where('import_index', $reviewIndex)
                ->first();

            $this->syncModel($review, CarReview::class, [
                'car_id' => $car->id,
                'import_index' => $reviewIndex,
                'type' => $type,
                'value' => $value,
            ], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncConfigurationGroups(Car $car, array $payload, array &$stats): void
    {
        foreach ($payload as $groupIndex => $groupPayload) {
            /** @var CarConfigurationGroup|null $group */
            $group = $car->configurationGroups()
                ->where('import_index', $groupIndex)
                ->first();

            /** @var CarConfigurationGroup $group */
            $group = $this->syncModel($group, CarConfigurationGroup::class, [
                'car_id' => $car->id,
                'name' => $this->normalizeString($groupPayload['name'] ?? null),
                'order' => $this->normalizeInteger($groupPayload['order'] ?? null),
                'import_index' => $groupIndex,
            ], $stats);

            $this->syncConfigurations($car, $group, $groupPayload['items'] ?? [], $stats);
            $this->syncEquipmentCategories($group, $groupPayload['equipment'] ?? [], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncConfigurations(Car $car, CarConfigurationGroup $group, array $payload, array &$stats): void
    {
        foreach ($payload as $configurationIndex => $configurationPayload) {
            /** @var CarConfiguration|null $configuration */
            $configuration = $group->configurations()
                ->where('import_index', $configurationIndex)
                ->first();

            $this->syncModel($configuration, CarConfiguration::class, [
                'car_id' => $car->id,
                'car_configuration_group_id' => $group->id,
                'import_index' => $configurationIndex,
                'price' => $this->normalizeInteger($configurationPayload['price'] ?? null),
                'engine_type' => $this->normalizeString($configurationPayload['engine_type'] ?? null),
                'engine_capacity' => $this->normalizeDecimal($configurationPayload['engine_capacity'] ?? null, 2),
                'horsepower' => $this->normalizeInteger($configurationPayload['horsepower'] ?? null),
                'transmission' => $this->normalizeString($configurationPayload['transmission'] ?? null),
                'drive_type' => $this->normalizeString($configurationPayload['drive_type'] ?? null),
                'fuel_city' => $this->normalizeDecimal($configurationPayload['fuel_city'] ?? null, 1),
                'fuel_highway' => $this->normalizeDecimal($configurationPayload['fuel_highway'] ?? null, 1),
                'fuel_combined' => $this->normalizeDecimal($configurationPayload['fuel_combined'] ?? null, 1),
                'acceleration' => $this->normalizeDecimal($configurationPayload['acceleration'] ?? null, 1),
                'speed' => $this->normalizeInteger($configurationPayload['speed'] ?? null),
            ], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncEquipmentCategories(CarConfigurationGroup $group, array $payload, array &$stats): void
    {
        foreach ($payload as $categoryIndex => $categoryPayload) {
            /** @var CarConfigurationEquipmentCategory|null $category */
            $category = $group->equipmentCategories()
                ->where('import_index', $categoryIndex)
                ->first();

            /** @var CarConfigurationEquipmentCategory $category */
            $category = $this->syncModel($category, CarConfigurationEquipmentCategory::class, [
                'car_configuration_group_id' => $group->id,
                'car_configuration_id' => null,
                'name' => $this->normalizeString($categoryPayload['name'] ?? null) ?? '',
                'import_index' => $categoryIndex,
            ], $stats);

            $this->syncEquipmentItems($category, $categoryPayload['items'] ?? [], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncEquipmentItems(CarConfigurationEquipmentCategory $category, array $payload, array &$stats): void
    {
        foreach ($payload as $itemIndex => $itemPayload) {
            /** @var CarConfigurationEquipment|null $item */
            $item = $category->items()
                ->where('import_index', $itemIndex)
                ->first();

            $this->syncModel($item, CarConfigurationEquipment::class, [
                'car_configuration_id' => null,
                'car_configuration_equipment_category_id' => $category->id,
                'import_index' => $itemIndex,
                'value' => $this->normalizeString($itemPayload['value'] ?? null),
                'is_extension' => $this->normalizeBoolean($itemPayload['is_extension'] ?? false),
                'price' => $this->normalizeInteger($itemPayload['price'] ?? null),
            ], $stats);
        }
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncModel(?Model $model, string $modelClass, array $attributes, array &$stats): Model
    {
        if ($model === null) {
            /** @var Model $created */
            $created = $modelClass::query()->create($attributes);
            $this->bump($stats, 'new');

            return $created;
        }

        $model->fill($attributes);

        if ($model->isDirty()) {
            $model->save();
            $this->bump($stats, 'updated');

            return $model;
        }

        $this->bump($stats, 'unchanged');

        return $model;
    }

    /**
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    private function freshStats(): array
    {
        return [
            'new' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'processed' => 0,
            'processed_cars' => 0,
        ];
    }

    /**
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
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

        return $normalized === '' ? null : $normalized;
    }

    private function normalizeBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    private function normalizeInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeDecimal(mixed $value, int $scale): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, $scale, '.', '');
    }
}
