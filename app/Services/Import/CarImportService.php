<?php

namespace App\Services\Import;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarDealer;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use App\Models\City;
use App\Models\Dealer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CarImportService
{
    /**
     * @param  array{
     *     cities?: array<int, array<string, mixed>>,
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
        $stats = $this->importCities($payload['cities'] ?? [], $stats);
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
     * @param  array<int, array<string, mixed>>  $citiesPayload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}|null  $stats
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    public function importCities(array $citiesPayload, ?array $stats = null): array
    {
        $stats ??= $this->freshStats();

        foreach ($citiesPayload as $cityPayload) {
            $this->upsertCity($cityPayload, $stats);
        }

        return $stats;
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
        $chunkContext = $this->buildChunkContext($carsPayload);

        foreach ($carsPayload as $carPayload) {
            if ($shouldStop !== null && $shouldStop()) {
                break;
            }

            DB::transaction(function () use ($carPayload, &$stats, &$chunkContext): void {
                $this->upsertCar($carPayload, $stats, $chunkContext);
            });

            $stats['processed_cars']++;

            if ($afterCarProcessed !== null) {
                $afterCarProcessed($stats);
            }
        }

        return $stats;
    }

    /**
     * @param  array<int, array<string, mixed>>  $carsPayload
     * @return array{
     *     brandsBySlug: array<string, Brand>,
     *     citiesBySlug: array<string, City>,
     *     dealersByName: array<string, Dealer>,
     *     carsBySlug: array<string, Car>
     * }
     */
    private function buildChunkContext(array $carsPayload): array
    {
        $brandSlugs = [];
        $citySlugs = [];
        $dealerNames = [];
        $carSlugs = [];

        foreach ($carsPayload as $carPayload) {
            $brandSlug = $this->normalizeString($carPayload['brand_slug'] ?? null);
            $carSlug = $this->normalizeString($carPayload['slug'] ?? null);

            if ($brandSlug !== null) {
                $brandSlugs[] = $brandSlug;
            }

            if ($carSlug !== null) {
                $carSlugs[] = $carSlug;
            }

            foreach (($carPayload['dealers'] ?? []) as $dealerPayload) {
                if (!is_array($dealerPayload)) {
                    continue;
                }

                $citySlug = $this->normalizeString($dealerPayload['city_slug'] ?? null);
                $dealerName = $this->normalizeString($dealerPayload['name'] ?? null);

                if ($citySlug !== null) {
                    $citySlugs[] = $citySlug;
                }

                if ($dealerName !== null) {
                    $dealerNames[] = $dealerName;
                }
            }
        }

        $brandsBySlug = Brand::query()
            ->whereIn('slug', array_values(array_unique($brandSlugs)), 'and', false)
            ->get()
            ->keyBy('slug')
            ->all();

        $citiesBySlug = City::query()
            ->whereIn('slug', array_values(array_unique($citySlugs)), 'and', false)
            ->get()
            ->keyBy('slug')
            ->all();

        $dealersByName = Dealer::query()
            ->whereIn('name', array_values(array_unique($dealerNames)), 'and', false)
            ->get()
            ->keyBy('name')
            ->all();

        /** @var array<string, Car> $carsBySlug */
        $carsBySlug = Car::query()
            ->whereIn('slug', array_values(array_unique($carSlugs)), 'and', false)
            ->with([
                'crashTest',
                'testDrives',
                'reviews',
                'photoGroups.photos',
                'carDealers',
                'configurationGroups.configurations',
                'configurationGroups.equipmentCategories.items',
            ])
            ->get()
            ->mapWithKeys(function (Car $car): array {
                $this->initializeCarRelations($car);

                foreach ($car->configurationGroups as $group) {
                    $this->initializeConfigurationGroupRelations($group);

                    foreach ($group->equipmentCategories as $category) {
                        $this->initializeEquipmentCategoryRelations($category);
                    }
                }

                foreach ($car->photoGroups as $group) {
                    $this->initializePhotoGroupRelations($group);
                }

                return [$car->slug => $car];
            })
            ->all();

        return [
            'brandsBySlug' => $brandsBySlug,
            'citiesBySlug' => $citiesBySlug,
            'dealersByName' => $dealersByName,
            'carsBySlug' => $carsBySlug,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function upsertCity(array $payload, array &$stats): City
    {
        $attributes = [
            'name' => $this->normalizeString($payload['name'] ?? null) ?? '',
            'slug' => $this->normalizeString($payload['slug'] ?? null) ?? '',
        ];

        /** @var City|null $city */
        $city = City::query()
            ->where('slug', $attributes['slug'])
            ->first();

        /** @var City $city */
        $city = $this->syncModel($city, City::class, $attributes, $stats);

        return $city;
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
    private function upsertCar(array $payload, array &$stats, array &$chunkContext): Car
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
        $brand = $chunkContext['brandsBySlug'][$brandSlug] ?? null;

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
            'cover_path' => $this->normalizeMediaPath($payload['cover_path'] ?? null),
        ];

        /** @var Car|null $car */
        $car = $chunkContext['carsBySlug'][$slug] ?? null;

        /** @var Car $car */
        $car = $this->syncModel($car, Car::class, $attributes, $stats);
        $this->initializeCarRelations($car);
        $chunkContext['carsBySlug'][$slug] = $car;

        $this->syncCrashTest($car, $payload['crash_test'] ?? null, $stats);
        $this->syncTestDrives($car, $payload['test_drives'] ?? [], $stats);

        if (array_key_exists('reviews', $payload) && is_array($payload['reviews'])) {
            $this->syncReviews($car, $payload['reviews'], $stats);
        }

        $this->syncPhotoGroups($car, $payload['photo_groups'] ?? [], $stats);
        $this->syncDealers($car, $payload['dealers'] ?? [], $stats, $chunkContext);
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
        $crashTest = $car->getRelation('crashTest');

        /** @var CarCrashTest $crashTest */
        $crashTest = $this->syncModel($crashTest, CarCrashTest::class, [
            'car_id' => $car->id,
            'year' => $this->normalizeInteger($payload['year'] ?? null),
            'rating' => $this->normalizeInteger($payload['rating'] ?? null),
            'video_path' => $this->normalizeString($payload['video_path'] ?? null),
        ], $stats);

        $car->setRelation('crashTest', $crashTest);
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncTestDrives(Car $car, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarTestDrive> $testDrives */
        $testDrives = $car->getRelation('testDrives');

        foreach ($payload as $testDriveIndex => $testDrivePayload) {
            $author = $this->normalizeString($testDrivePayload['author'] ?? null) ?? '';
            $videoPath = $this->normalizeString($testDrivePayload['path'] ?? null) ?? '';

            /** @var CarTestDrive|null $testDrive */
            $testDrive = $testDrives->firstWhere('import_index', $testDriveIndex);

            $wasMissing = $testDrive === null;

            /** @var CarTestDrive $testDrive */
            $testDrive = $this->syncModel($testDrive, CarTestDrive::class, [
                'car_id' => $car->id,
                'import_index' => $testDriveIndex,
                'author' => $author,
                'video_path' => $videoPath,
            ], $stats);

            if ($wasMissing) {
                $testDrives->push($testDrive);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncReviews(Car $car, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarReview> $reviews */
        $reviews = $car->getRelation('reviews');

        foreach ($payload as $reviewIndex => $reviewPayload) {
            $type = $this->normalizeString($reviewPayload['type'] ?? null) ?? '';
            $value = $this->normalizeString($reviewPayload['value'] ?? null) ?? '';

            /** @var CarReview|null $review */
            $review = $reviews->firstWhere('import_index', $reviewIndex);

            $wasMissing = $review === null;

            /** @var CarReview $review */
            $review = $this->syncModel($review, CarReview::class, [
                'car_id' => $car->id,
                'import_index' => $reviewIndex,
                'type' => $type,
                'value' => $value,
            ], $stats);

            if ($wasMissing) {
                $reviews->push($review);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncPhotoGroups(Car $car, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarPhotoGroup> $photoGroups */
        $photoGroups = $car->getRelation('photoGroups');

        foreach ($payload as $groupPayload) {
            $groupName = $this->normalizeString($groupPayload['name'] ?? null) ?? '';

            /** @var CarPhotoGroup|null $group */
            $group = $photoGroups->firstWhere('name', $groupName);
            $wasMissing = $group === null;

            /** @var CarPhotoGroup $group */
            $group = $this->syncModel($group, CarPhotoGroup::class, [
                'car_id' => $car->id,
                'name' => $groupName,
            ], $stats);
            $this->initializePhotoGroupRelations($group);

            if ($wasMissing) {
                $photoGroups->push($group);
            }

            $this->syncPhotos($car, $group, $groupPayload['photo_list'] ?? [], $stats);
        }
    }

    /**
     * @param  array<int, mixed>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncPhotos(Car $car, CarPhotoGroup $group, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarPhoto> $photos */
        $photos = $group->getRelation('photos');

        foreach ($payload as $photoPath) {
            $normalizedPhotoPath = $this->normalizeMediaPath($photoPath);

            if ($normalizedPhotoPath === null) {
                continue;
            }

            /** @var CarPhoto|null $photo */
            $photo = $photos->firstWhere('photo_path', $normalizedPhotoPath);
            $wasMissing = $photo === null;

            /** @var CarPhoto $photo */
            $photo = $this->syncModel($photo, CarPhoto::class, [
                'car_id' => $car->id,
                'car_photo_group_id' => $group->id,
                'photo_path' => $normalizedPhotoPath,
            ], $stats);

            if ($wasMissing) {
                $photos->push($photo);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncDealers(Car $car, array $payload, array &$stats, array &$chunkContext): void
    {
        /** @var EloquentCollection<int, CarDealer> $carDealers */
        $carDealers = $car->getRelation('carDealers');

        foreach ($payload as $dealerPayload) {
            $dealerName = $this->normalizeString($dealerPayload['name'] ?? null) ?? '';
            $citySlug = $this->normalizeString($dealerPayload['city_slug'] ?? null);

            if ($citySlug === null) {
                throw new RuntimeException("Не указан city_slug у дилера \"{$dealerName}\".");
            }

            /** @var City|null $city */
            $city = $chunkContext['citiesBySlug'][$citySlug] ?? null;

            if ($city === null) {
                throw new RuntimeException("Город со slug \"{$citySlug}\" не найден для дилера \"{$dealerName}\".");
            }

            /** @var Dealer|null $dealer */
            $dealer = $chunkContext['dealersByName'][$dealerName] ?? null;

            /** @var Dealer $dealer */
            $dealer = $this->syncModel($dealer, Dealer::class, [
                'name' => $dealerName,
            ], $stats);
            $chunkContext['dealersByName'][$dealerName] = $dealer;

            /** @var CarDealer|null $carDealer */
            $carDealer = $carDealers->first(function (CarDealer $candidate) use ($dealer, $city): bool {
                return $candidate->dealer_id === $dealer->id
                    && $candidate->city_id === $city->id;
            });

            $wasMissing = $carDealer === null;

            /** @var CarDealer $carDealer */
            $carDealer = $this->syncModel($carDealer, CarDealer::class, [
                'car_id' => $car->id,
                'city_id' => $city->id,
                'dealer_id' => $dealer->id,
                'address' => $this->normalizeString($dealerPayload['address'] ?? null),
                'phone' => $this->normalizeString($dealerPayload['phone'] ?? null),
                'website' => $this->normalizeString($dealerPayload['website'] ?? $dealerPayload['url'] ?? null),
                'is_official' => $this->normalizeBoolean(
                    $dealerPayload['is_official'] ?? $dealerPayload['is_official_deler'] ?? false
                ),
            ], $stats);

            if ($wasMissing) {
                $carDealers->push($carDealer);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncConfigurationGroups(Car $car, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarConfigurationGroup> $configurationGroups */
        $configurationGroups = $car->getRelation('configurationGroups');

        foreach ($payload as $groupIndex => $groupPayload) {
            /** @var CarConfigurationGroup|null $group */
            $group = $configurationGroups->firstWhere('import_index', $groupIndex);
            $wasMissing = $group === null;

            /** @var CarConfigurationGroup $group */
            $group = $this->syncModel($group, CarConfigurationGroup::class, [
                'car_id' => $car->id,
                'name' => $this->normalizeString($groupPayload['name'] ?? null),
                'order' => $this->normalizeInteger($groupPayload['order'] ?? null),
                'import_index' => $groupIndex,
            ], $stats);
            $this->initializeConfigurationGroupRelations($group);

            if ($wasMissing) {
                $configurationGroups->push($group);
            }

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
        /** @var EloquentCollection<int, CarConfiguration> $configurations */
        $configurations = $group->getRelation('configurations');

        foreach ($payload as $configurationIndex => $configurationPayload) {
            /** @var CarConfiguration|null $configuration */
            $configuration = $configurations->firstWhere('import_index', $configurationIndex);
            $wasMissing = $configuration === null;

            /** @var CarConfiguration $configuration */
            $configuration = $this->syncModel($configuration, CarConfiguration::class, [
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
                'acceleration' => $this->normalizeAcceleration($configurationPayload['acceleration'] ?? null),
                'speed' => $this->normalizeInteger($configurationPayload['speed'] ?? null),
            ], $stats);

            if ($wasMissing) {
                $configurations->push($configuration);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncEquipmentCategories(CarConfigurationGroup $group, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarConfigurationEquipmentCategory> $categories */
        $categories = $group->getRelation('equipmentCategories');

        foreach ($payload as $categoryIndex => $categoryPayload) {
            /** @var CarConfigurationEquipmentCategory|null $category */
            $category = $categories->firstWhere('import_index', $categoryIndex);
            $wasMissing = $category === null;

            /** @var CarConfigurationEquipmentCategory $category */
            $category = $this->syncModel($category, CarConfigurationEquipmentCategory::class, [
                'car_configuration_group_id' => $group->id,
                'car_configuration_id' => null,
                'name' => $this->normalizeString($categoryPayload['name'] ?? null) ?? '',
                'import_index' => $categoryIndex,
            ], $stats);
            $this->initializeEquipmentCategoryRelations($category);

            if ($wasMissing) {
                $categories->push($category);
            }

            $this->syncEquipmentItems($category, $categoryPayload['items'] ?? [], $stats);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payload
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function syncEquipmentItems(CarConfigurationEquipmentCategory $category, array $payload, array &$stats): void
    {
        /** @var EloquentCollection<int, CarConfigurationEquipment> $items */
        $items = $category->getRelation('items');

        foreach ($payload as $itemIndex => $itemPayload) {
            /** @var CarConfigurationEquipment|null $item */
            $item = $items->firstWhere('import_index', $itemIndex);
            $wasMissing = $item === null;

            /** @var CarConfigurationEquipment $item */
            $item = $this->syncModel($item, CarConfigurationEquipment::class, [
                'car_configuration_id' => null,
                'car_configuration_equipment_category_id' => $category->id,
                'import_index' => $itemIndex,
                'value' => $this->normalizeString($itemPayload['value'] ?? null),
                'is_extension' => $this->normalizeBoolean($itemPayload['is_extension'] ?? false),
                'price' => $this->normalizeInteger($itemPayload['price'] ?? null),
            ], $stats);

            if ($wasMissing) {
                $items->push($item);
            }
        }
    }

    private function initializeCarRelations(Car $car): void
    {
        if (!$car->relationLoaded('testDrives')) {
            $car->setRelation('testDrives', new EloquentCollection());
        }

        if (!$car->relationLoaded('reviews')) {
            $car->setRelation('reviews', new EloquentCollection());
        }

        if (!$car->relationLoaded('photoGroups')) {
            $car->setRelation('photoGroups', new EloquentCollection());
        }

        if (!$car->relationLoaded('carDealers')) {
            $car->setRelation('carDealers', new EloquentCollection());
        }

        if (!$car->relationLoaded('configurationGroups')) {
            $car->setRelation('configurationGroups', new EloquentCollection());
        }

        if (!$car->relationLoaded('crashTest')) {
            $car->setRelation('crashTest', null);
        }
    }

    private function initializePhotoGroupRelations(CarPhotoGroup $group): void
    {
        if (!$group->relationLoaded('photos')) {
            $group->setRelation('photos', new EloquentCollection());
        }
    }

    private function initializeConfigurationGroupRelations(CarConfigurationGroup $group): void
    {
        if (!$group->relationLoaded('configurations')) {
            $group->setRelation('configurations', new EloquentCollection());
        }

        if (!$group->relationLoaded('equipmentCategories')) {
            $group->setRelation('equipmentCategories', new EloquentCollection());
        }
    }

    private function initializeEquipmentCategoryRelations(CarConfigurationEquipmentCategory $category): void
    {
        if (!$category->relationLoaded('items')) {
            $category->setRelation('items', new EloquentCollection());
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

    private function normalizeAcceleration(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numericValue = (float) $value;

        if ($numericValue < 0) {
            throw new RuntimeException('Значение acceleration не может быть отрицательным.');
        }

        if ($numericValue > 999.9) {
            $numericValue /= 1000;
        }

        if ($numericValue > 999.9) {
            throw new RuntimeException("Значение acceleration \"{$value}\" выходит за допустимый диапазон.");
        }

        return number_format($numericValue, 1, '.', '');
    }

    private function normalizeMediaPath(mixed $value): ?string
    {
        $path = $this->normalizeString($value);

        if ($path === null) {
            return null;
        }

        $normalized = ltrim($path, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        return $normalized === '' ? null : $normalized;
    }
}
