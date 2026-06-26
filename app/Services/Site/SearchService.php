<?php

namespace App\Services\Site;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SearchService
{
    private const MIN_QUERY_LENGTH = 2;

    private const DEFAULT_BRAND_SUGGEST_LIMIT = 6;

    private const DEFAULT_MODEL_SUGGEST_LIMIT = 8;

    private const DEFAULT_MODEL_PAGE_SIZE = 30;

    private const DEFAULT_SORT = 'popular';

    private const SORT_OPTIONS = [
        'popular' => 'По популярности',
        'price_asc' => 'Сначала дешевле',
        'price_desc' => 'Сначала дороже',
    ];

    private const ENGINE_TYPE_GROUPS = [
        'petrol' => ['бензин', 'petrol', 'gasoline'],
        'diesel' => ['дизель', 'diesel'],
        'hybrid' => ['гибрид', 'hybrid', 'hev', 'mhev', 'phev', 'plugin-hybrid', 'plug-in hybrid'],
        'electric' => ['электро', 'electric', 'ev', 'bev'],
    ];

    private const ENGINE_TYPE_LABELS = [
        'petrol' => 'Бензин',
        'diesel' => 'Дизель',
        'hybrid' => 'Гибрид',
        'electric' => 'Электро',
    ];

    private const TRANSMISSION_GROUPS = [
        'automatic' => ['AT', 'АТ', 'automatic', 'auto', 'single-speed'],
        'manual' => ['MT', 'МТ', 'manual'],
        'robot' => ['AMT', 'DCT', 'робот', 'robot'],
        'variator' => ['CVT', 'вариатор', 'variator'],
    ];

    private const TRANSMISSION_LABELS = [
        'automatic' => 'АКПП',
        'manual' => 'МКПП',
        'robot' => 'Робот',
        'variator' => 'Вариатор',
    ];

    private const DRIVE_TYPE_GROUPS = [
        'full' => ['4x4 Полный', 'Полный', 'awd', '4wd', '4x4', 'full'],
        'front' => ['Передний', 'fwd', 'front'],
        'rear' => ['Задний', 'rwd', 'rear'],
    ];

    private const DRIVE_TYPE_LABELS = [
        'full' => 'Полный',
        'front' => 'Передний',
        'rear' => 'Задний',
    ];

    public function suggest(
        ?string $rawQuery,
        int $brandLimit = self::DEFAULT_BRAND_SUGGEST_LIMIT,
        int $modelLimit = self::DEFAULT_MODEL_SUGGEST_LIMIT
    ): array
    {
        $query = $this->normalizeQuery($rawQuery);

        if (! $this->canSearchTextQuery($query)) {
            return [
                'query' => $query,
                'brands' => collect(),
                'models' => collect(),
            ];
        }

        return [
            'query' => $query,
            'brands' => $this->brandsQuery($query)
                ->limit(max(1, $brandLimit))
                ->get(),
            'models' => $this->carsQuery($query, [])
                ->limit(max(1, $modelLimit))
                ->get(),
        ];
    }

    public function search(
        ?string $rawQuery,
        array $rawFilters = [],
        mixed $rawSort = null,
        int $page = 1,
        int $perPage = self::DEFAULT_MODEL_PAGE_SIZE
    ): array
    {
        $query = $this->normalizeQuery($rawQuery);
        $filters = $this->normalizeFilters($rawFilters);
        $sort = $this->normalizeSort($rawSort);
        $filterOptions = $this->filterOptions();
        $brandOptions = $this->brandOptions();
        $rangeBounds = $this->rangeBounds();
        $sortOptions = $this->sortOptions();
        $hasSearchableQuery = $this->canSearchTextQuery($query);
        $hasActiveFilters = $this->hasActiveFilters($filters);
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        if (! $this->canSearch($query, $filters)) {
            return [
                'query' => $query,
                'queryTooShort' => $this->isQueryTooShort($query),
                'hasSearchableQuery' => $hasSearchableQuery,
                'hasActiveFilters' => $hasActiveFilters,
                'filters' => $filters,
                'filterOptions' => $filterOptions,
                'brandOptions' => $brandOptions,
                'rangeBounds' => $rangeBounds,
                'sort' => $sort,
                'sortOptions' => $sortOptions,
                'brands' => collect(),
                'models' => $this->carsQuery('', [], $sort)
                    ->paginate($perPage, ['*'], 'page', $page)
                    ->withQueryString(),
            ];
        }

        return [
            'query' => $query,
            'queryTooShort' => $this->isQueryTooShort($query),
            'hasSearchableQuery' => $hasSearchableQuery,
            'hasActiveFilters' => $hasActiveFilters,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'brandOptions' => $brandOptions,
            'rangeBounds' => $rangeBounds,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'brands' => $hasSearchableQuery
                ? $this->brandsQuery($query)
                    ->withCount('cars')
                    ->get()
                : collect(),
            'models' => $this->carsQuery($query, $filters, $sort)
                ->paginate($perPage, ['*'], 'page', $page)
                ->withQueryString(),
        ];
    }

    public function minQueryLength(): int
    {
        return self::MIN_QUERY_LENGTH;
    }

    private function filterOptions(): array
    {
        return [
            'engine_types' => $this->mappedOptionsForColumn(
                'engine_type',
                self::ENGINE_TYPE_GROUPS,
                self::ENGINE_TYPE_LABELS
            ),
            'transmissions' => $this->mappedOptionsForColumn(
                'transmission',
                self::TRANSMISSION_GROUPS,
                self::TRANSMISSION_LABELS
            ),
            'drive_types' => $this->mappedOptionsForColumn(
                'drive_type',
                self::DRIVE_TYPE_GROUPS,
                self::DRIVE_TYPE_LABELS
            ),
        ];
    }

    private function brandOptions(): Collection
    {
        return Brand::query()
            ->select(['id', 'name', 'slug'])
            ->whereHas('cars')
            ->orderBy('name')
            ->get();
    }

    private function sortOptions(): array
    {
        return self::SORT_OPTIONS;
    }

    private function rangeBounds(): array
    {
        $startPriceExpression = $this->numericPriceExpression('start_price');
        $endPriceExpression = $this->numericPriceExpression('end_price');

        $configurationBounds = CarConfiguration::query()
            ->selectRaw(
                'MIN(CASE WHEN price > 0 THEN price END) as price_min, MAX(CASE WHEN price > 0 THEN price END) as price_max, '.
                'MIN(CASE WHEN engine_capacity > 0 THEN engine_capacity END) as engine_capacity_min, MAX(CASE WHEN engine_capacity > 0 THEN engine_capacity END) as engine_capacity_max, '.
                'MIN(CASE WHEN fuel_combined > 0 THEN fuel_combined END) as fuel_combined_min, MAX(CASE WHEN fuel_combined > 0 THEN fuel_combined END) as fuel_combined_max, '.
                'MIN(CASE WHEN horsepower > 0 THEN horsepower END) as horsepower_min, MAX(CASE WHEN horsepower > 0 THEN horsepower END) as horsepower_max, '.
                'MIN(CASE WHEN acceleration > 0 THEN acceleration END) as acceleration_min, MAX(CASE WHEN acceleration > 0 THEN acceleration END) as acceleration_max',
                []
            )
            ->first();

        $carPriceBounds = Car::query()
            ->selectRaw(
                "MIN(CASE WHEN {$startPriceExpression} > 0 THEN {$startPriceExpression} END) as start_price_min, ".
                "MIN(CASE WHEN {$endPriceExpression} > 0 THEN {$endPriceExpression} END) as end_price_min, ".
                "MAX(CASE WHEN {$startPriceExpression} > 0 THEN {$startPriceExpression} END) as start_price_max, ".
                "MAX(CASE WHEN {$endPriceExpression} > 0 THEN {$endPriceExpression} END) as end_price_max",
                []
            )
            ->first();

        $priceMin = $this->minimumPositiveBound([
            $configurationBounds->price_min ?? null,
            $carPriceBounds->start_price_min ?? null,
            $carPriceBounds->end_price_min ?? null,
        ]);
        $priceMax = $this->maximumPositiveBound([
            $configurationBounds->price_max ?? null,
            $carPriceBounds->start_price_max ?? null,
            $carPriceBounds->end_price_max ?? null,
        ]);

        return [
            'price' => $this->buildRangeBounds($priceMin, $priceMax, 10000, 1000000, 10000000, 20),
            'engine_capacity' => $this->buildRangeBounds($configurationBounds->engine_capacity_min ?? null, $configurationBounds->engine_capacity_max ?? null, 0.1, 1.0, 4.0, 10),
            'fuel_combined' => $this->buildRangeBounds($configurationBounds->fuel_combined_min ?? null, $configurationBounds->fuel_combined_max ?? null, 0.1, 5.0, 15.0, 10),
            'horsepower' => $this->buildRangeBounds($configurationBounds->horsepower_min ?? null, $configurationBounds->horsepower_max ?? null, 1, 100, 400, 60),
            'acceleration' => $this->buildRangeBounds($configurationBounds->acceleration_min ?? null, $configurationBounds->acceleration_max ?? null, 0.1, 5.0, 15.0, 20),
        ];
    }

    private function buildRangeBounds(
        mixed $rawMin,
        mixed $rawMax,
        float $step,
        float $fallbackMin,
        float $fallbackMax,
        int $minimumStepCount = 6
    ): array {
        $step = max($step, 0.1);
        $minimumSpan = $step * max(1, $minimumStepCount);
        $min = $this->normalizePositiveBound($rawMin);
        $max = $this->normalizePositiveBound($rawMax);

        if ($min === null || $max === null) {
            $min = $fallbackMin;
            $max = $fallbackMax;
        }

        if ($max < $min) {
            [$min, $max] = [$max, $min];
        }

        $min = $this->snapRangeDown($min, $step);
        $max = $this->snapRangeUp($max, $step);

        if (($max - $min) < $minimumSpan) {
            $center = ($min + $max) / 2;
            $halfSpan = $minimumSpan / 2;
            $min = max(0.0, $this->snapRangeDown($center - $halfSpan, $step));
            $max = $this->snapRangeUp($center + $halfSpan, $step);
        }

        if ($max <= $min) {
            $max = $min + $step;
        }

        return [
            'min' => $this->castRangeNumber($min, $step),
            'max' => $this->castRangeNumber($max, $step),
            'step' => $this->castRangeNumber($step, $step),
        ];
    }

    private function normalizePositiveBound(mixed $value): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $normalized = (float) $value;

        return $normalized > 0 ? $normalized : null;
    }

    private function minimumPositiveBound(array $values): ?float
    {
        $normalized = array_values(array_filter(
            array_map(fn (mixed $value): ?float => $this->normalizePositiveBound($value), $values),
            static fn (?float $value): bool => $value !== null
        ));

        return $normalized === [] ? null : min($normalized);
    }

    private function maximumPositiveBound(array $values): ?float
    {
        $normalized = array_values(array_filter(
            array_map(fn (mixed $value): ?float => $this->normalizePositiveBound($value), $values),
            static fn (?float $value): bool => $value !== null
        ));

        return $normalized === [] ? null : max($normalized);
    }

    private function snapRangeDown(float $value, float $step): float
    {
        return floor(($value + 0.000001) / $step) * $step;
    }

    private function snapRangeUp(float $value, float $step): float
    {
        return ceil(($value - 0.000001) / $step) * $step;
    }

    private function castRangeNumber(float $value, float $step): int|float
    {
        $precision = $this->rangeStepPrecision($step);
        $normalized = round($value, $precision);

        return $precision === 0 ? (int) $normalized : $normalized;
    }

    private function rangeStepPrecision(float $step): int
    {
        $normalized = rtrim(rtrim(number_format($step, 6, '.', ''), '0'), '.');
        $position = strrpos($normalized, '.');

        return $position === false ? 0 : strlen($normalized) - $position - 1;
    }

    private function normalizeBrand(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $slug = trim((string) $value);

        if ($slug === '') {
            return null;
        }

        $exists = Brand::query()
            ->where('slug', $slug)
            ->whereHas('cars')
            ->exists();

        return $exists ? $slug : null;
    }

    private function brandsQuery(string $query): Builder
    {
        $contains = '%'.$query.'%';
        $startsWith = $query.'%';

        return Brand::query()
            ->select(['id', 'name', 'slug'])
            ->whereHas('cars')
            ->where(function (Builder $builder) use ($contains): void {
                $builder
                    ->where('name', 'like', $contains)
                    ->orWhere('slug', 'like', $contains);
            })
            ->orderByRaw(
                'CASE WHEN name LIKE ? THEN 0 WHEN slug LIKE ? THEN 1 ELSE 2 END',
                [$startsWith, $startsWith]
            )
            ->popular()
            ->orderBy('name');
    }

    private function carsQuery(string $query, array $filters, string $sort = self::DEFAULT_SORT): Builder
    {
        $builder = Car::query()
            ->select([
                'id',
                'brand_id',
                'name',
                'slug',
                'year',
                'cover_path',
                'start_price',
                'end_price',
                'is_soon',
                'is_electric_car',
                'views_count',
            ])
            ->with([
                'brand:id,name,slug',
                'configurations:id,car_id,price,currency',
            ])
            ->whereHas('brand');

        if (($filters['brand'] ?? null) !== null) {
            $builder->whereHas('brand', function (Builder $brandBuilder) use ($filters): void {
                $brandBuilder->where('slug', $filters['brand']);
            });
        }

        if ($this->canSearchTextQuery($query)) {
            $contains = '%'.$query.'%';
            $startsWith = $query.'%';

            $builder
                ->where(function (Builder $searchBuilder) use ($contains): void {
                    $searchBuilder
                        ->where('cars.name', 'like', $contains)
                        ->orWhere('cars.slug', 'like', $contains)
                        ->orWhereHas('brand', function (Builder $brandBuilder) use ($contains): void {
                            $brandBuilder
                                ->where('name', 'like', $contains)
                                ->orWhere('slug', 'like', $contains);
                        });
                })
                ->orderByRaw(
                    'CASE WHEN cars.name LIKE ? THEN 0 WHEN cars.slug LIKE ? THEN 1 ELSE 2 END',
                    [$startsWith, $startsWith]
                );
        }

        if ($this->hasConfigurationFilters($filters)) {
            $builder->whereHas('configurations', function (Builder $configurationBuilder) use ($filters): void {
                $this->applyConfigurationFilters($configurationBuilder, $filters);
            });
        }

        if ($this->hasPriceFilters($filters)) {
            $builder->where(function (Builder $priceBuilder) use ($filters): void {
                $priceBuilder
                    ->whereHas('configurations', function (Builder $configurationBuilder) use ($filters): void {
                        $this->applyConfigurationPriceFilters($configurationBuilder, $filters);
                    })
                    ->orWhere(function (Builder $carPriceBuilder) use ($filters): void {
                        $this->applyCarPriceFilters($carPriceBuilder, $filters);
                    });
            });
        }

        return $this->applySorting($builder, $sort);
    }

    private function normalizeQuery(?string $query): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim((string) $query));

        return is_string($normalized) ? $normalized : '';
    }

    private function normalizeFilters(array $rawFilters): array
    {
        [$priceMin, $priceMax] = $this->normalizeIntegerRange(
            $rawFilters['price_min'] ?? null,
            $rawFilters['price_max'] ?? null
        );

        [$engineCapacityMin, $engineCapacityMax] = $this->normalizeDecimalRange(
            $rawFilters['engine_capacity_min'] ?? null,
            $rawFilters['engine_capacity_max'] ?? null
        );

        [$fuelCombinedMin, $fuelCombinedMax] = $this->normalizeDecimalRange(
            $rawFilters['fuel_combined_min'] ?? null,
            $rawFilters['fuel_combined_max'] ?? null
        );

        [$horsepowerMin, $horsepowerMax] = $this->normalizeIntegerRange(
            $rawFilters['horsepower_min'] ?? null,
            $rawFilters['horsepower_max'] ?? null
        );

        [$accelerationMin, $accelerationMax] = $this->normalizeDecimalRange(
            $rawFilters['acceleration_min'] ?? null,
            $rawFilters['acceleration_max'] ?? null
        );

        return [
            'brand' => $this->normalizeBrand($rawFilters['brand'] ?? null),
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'engine_capacity_min' => $engineCapacityMin,
            'engine_capacity_max' => $engineCapacityMax,
            'engine_types' => $this->normalizeMultiSelect(
                $rawFilters['engine_types'] ?? [],
                array_keys(self::ENGINE_TYPE_LABELS)
            ),
            'transmissions' => $this->normalizeMultiSelect(
                $rawFilters['transmissions'] ?? [],
                array_keys(self::TRANSMISSION_LABELS)
            ),
            'fuel_combined_min' => $fuelCombinedMin,
            'fuel_combined_max' => $fuelCombinedMax,
            'drive_types' => $this->normalizeMultiSelect(
                $rawFilters['drive_types'] ?? [],
                array_keys(self::DRIVE_TYPE_LABELS)
            ),
            'horsepower_min' => $horsepowerMin,
            'horsepower_max' => $horsepowerMax,
            'acceleration_min' => $accelerationMin,
            'acceleration_max' => $accelerationMax,
        ];
    }

    private function normalizeSort(mixed $value): string
    {
        if (! is_scalar($value)) {
            return self::DEFAULT_SORT;
        }

        $sort = trim((string) $value);

        return array_key_exists($sort, self::SORT_OPTIONS) ? $sort : self::DEFAULT_SORT;
    }

    private function normalizeMultiSelect(mixed $value, array $allowedValues): array
    {
        $items = is_array($value) ? $value : [$value];
        $normalized = [];

        foreach ($items as $item) {
            if (! is_scalar($item)) {
                continue;
            }

            $key = trim((string) $item);

            if ($key === '' || ! in_array($key, $allowedValues, true)) {
                continue;
            }

            $normalized[$key] = $key;
        }

        return array_values($normalized);
    }

    private function normalizeIntegerRange(mixed $rawMin, mixed $rawMax): array
    {
        $min = $this->normalizeInteger($rawMin);
        $max = $this->normalizeInteger($rawMax);

        if ($min !== null && $max !== null && $min > $max) {
            return [$max, $min];
        }

        return [$min, $max];
    }

    private function normalizeDecimalRange(mixed $rawMin, mixed $rawMax): array
    {
        $min = $this->normalizeDecimal($rawMin);
        $max = $this->normalizeDecimal($rawMax);

        if ($min !== null && $max !== null && $min > $max) {
            return [$max, $min];
        }

        return [$min, $max];
    }

    private function normalizeInteger(mixed $value): ?int
    {
        if (! is_scalar($value)) {
            return null;
        }

        $normalized = preg_replace('/[^\d]/', '', (string) $value);

        if (! is_string($normalized) || $normalized === '') {
            return null;
        }

        return (int) $normalized;
    }

    private function normalizeDecimal(mixed $value): ?float
    {
        if (! is_scalar($value)) {
            return null;
        }

        $normalized = str_replace(',', '.', trim((string) $value));

        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        return max(0, (float) $normalized);
    }

    private function hasActiveFilters(array $filters): bool
    {
        foreach ($filters as $value) {
            if (is_array($value) && $value !== []) {
                return true;
            }

            if (! is_array($value) && $value !== null) {
                return true;
            }
        }

        return false;
    }

    private function hasConfigurationFilters(array $filters): bool
    {
        return ($filters['engine_capacity_min'] ?? null) !== null
            || ($filters['engine_capacity_max'] ?? null) !== null
            || ($filters['engine_types'] ?? []) !== []
            || ($filters['transmissions'] ?? []) !== []
            || ($filters['fuel_combined_min'] ?? null) !== null
            || ($filters['fuel_combined_max'] ?? null) !== null
            || ($filters['drive_types'] ?? []) !== []
            || ($filters['horsepower_min'] ?? null) !== null
            || ($filters['horsepower_max'] ?? null) !== null
            || ($filters['acceleration_min'] ?? null) !== null
            || ($filters['acceleration_max'] ?? null) !== null;
    }

    private function hasPriceFilters(array $filters): bool
    {
        return ($filters['price_min'] ?? null) !== null || ($filters['price_max'] ?? null) !== null;
    }

    private function canSearch(string $query, array $filters): bool
    {
        return $this->canSearchTextQuery($query) || $this->hasActiveFilters($filters);
    }

    private function canSearchTextQuery(string $query): bool
    {
        return $query !== '' && mb_strlen($query) >= self::MIN_QUERY_LENGTH;
    }

    private function isQueryTooShort(string $query): bool
    {
        return $query !== '' && ! $this->canSearchTextQuery($query);
    }

    private function applyConfigurationFilters(Builder $builder, array $filters): void
    {
        if ($filters['engine_capacity_min'] !== null) {
            $builder->where('engine_capacity', '>=', $filters['engine_capacity_min']);
        }

        if ($filters['engine_capacity_max'] !== null) {
            $builder->where('engine_capacity', '<=', $filters['engine_capacity_max']);
        }

        if ($filters['engine_types'] !== []) {
            $this->applyKeywordFilter($builder, 'engine_type', $filters['engine_types'], self::ENGINE_TYPE_GROUPS);
        }

        if ($filters['transmissions'] !== []) {
            $this->applyKeywordFilter($builder, 'transmission', $filters['transmissions'], self::TRANSMISSION_GROUPS);
        }

        if ($filters['fuel_combined_min'] !== null) {
            $builder->where('fuel_combined', '>=', $filters['fuel_combined_min']);
        }

        if ($filters['fuel_combined_max'] !== null) {
            $builder->where('fuel_combined', '<=', $filters['fuel_combined_max']);
        }

        if ($filters['drive_types'] !== []) {
            $this->applyKeywordFilter($builder, 'drive_type', $filters['drive_types'], self::DRIVE_TYPE_GROUPS);
        }

        if ($filters['horsepower_min'] !== null) {
            $builder->where('horsepower', '>=', $filters['horsepower_min']);
        }

        if ($filters['horsepower_max'] !== null) {
            $builder->where('horsepower', '<=', $filters['horsepower_max']);
        }

        if ($filters['acceleration_min'] !== null) {
            $builder->where('acceleration', '>=', $filters['acceleration_min']);
        }

        if ($filters['acceleration_max'] !== null) {
            $builder->where('acceleration', '<=', $filters['acceleration_max']);
        }
    }

    private function applyConfigurationPriceFilters(Builder $builder, array $filters): void
    {
        if ($filters['price_min'] !== null) {
            $builder->where('price', '>=', $filters['price_min']);
        }

        if ($filters['price_max'] !== null) {
            $builder->where('price', '<=', $filters['price_max']);
        }
    }

    private function applyCarPriceFilters(Builder $builder, array $filters): void
    {
        $startPriceExpression = $this->numericPriceExpression('start_price');
        $endPriceExpression = $this->numericPriceExpression('end_price');

        if ($filters['price_min'] !== null) {
            $builder->where(function (Builder $priceBuilder) use ($filters, $startPriceExpression, $endPriceExpression): void {
                $priceBuilder
                    ->whereRaw("{$startPriceExpression} >= ?", [$filters['price_min']])
                    ->orWhere(function (Builder $fallbackBuilder) use ($filters, $endPriceExpression): void {
                        $fallbackBuilder
                            ->where(function (Builder $missingStartPriceBuilder): void {
                                $missingStartPriceBuilder
                                    ->whereNull('start_price')
                                    ->orWhere('start_price', '');
                            })
                            ->whereRaw("{$endPriceExpression} >= ?", [$filters['price_min']]);
                    });
            });
        }

        if ($filters['price_max'] !== null) {
            $builder->where(function (Builder $priceBuilder) use ($filters, $startPriceExpression, $endPriceExpression): void {
                $priceBuilder
                    ->whereRaw("{$endPriceExpression} <= ?", [$filters['price_max']])
                    ->orWhere(function (Builder $fallbackBuilder) use ($filters, $startPriceExpression): void {
                        $fallbackBuilder
                            ->where(function (Builder $missingEndPriceBuilder): void {
                                $missingEndPriceBuilder
                                    ->whereNull('end_price')
                                    ->orWhere('end_price', '');
                            })
                            ->whereRaw("{$startPriceExpression} <= ?", [$filters['price_max']]);
                    });
            });
        }
    }

    private function numericPriceExpression(string $column): string
    {
        $driver = Car::query()->getConnection()->getDriverName();

        return match ($driver) {
            'mysql', 'mariadb' => "CAST({$column} AS UNSIGNED)",
            'pgsql' => "CAST({$column} AS INTEGER)",
            default => "CAST({$column} AS INTEGER)",
        };
    }

    private function displayPriceExpression(): string
    {
        $startPriceExpression = $this->numericPriceExpression('start_price');
        $endPriceExpression = $this->numericPriceExpression('end_price');

        return "CASE WHEN COALESCE(start_price, '') != '' THEN {$startPriceExpression} WHEN COALESCE(end_price, '') != '' THEN {$endPriceExpression} ELSE NULL END";
    }

    private function applySorting(Builder $builder, string $sort): Builder
    {
        if ($sort === self::DEFAULT_SORT) {
            return $builder
                ->popular()
                ->orderBy('cars.name');
        }

        $priceExpression = $this->displayPriceExpression();
        $direction = $sort === 'price_desc' ? 'DESC' : 'ASC';

        return $builder
            ->reorder()
            ->orderByRaw("CASE WHEN {$priceExpression} IS NULL THEN 1 ELSE 0 END")
            ->orderByRaw("{$priceExpression} {$direction}")
            ->orderBy('cars.name');
    }

    private function mappedOptionsForColumn(string $column, array $groups, array $labels): array
    {
        $availableKeys = [];

        $values = CarConfiguration::query()
            ->where($column, '!=', '')
            ->distinct()
            ->get([$column])
            ->pluck($column);

        foreach ($values as $value) {
            $key = $this->canonicalKeyForValue((string) $value, $groups);

            if ($key !== null) {
                $availableKeys[$key] = true;
            }
        }

        $options = [];

        foreach ($labels as $key => $label) {
            if (! isset($availableKeys[$key])) {
                continue;
            }

            $options[] = [
                'value' => $key,
                'label' => $label,
            ];
        }

        return $options;
    }

    private function canonicalKeyForValue(string $value, array $groups): ?string
    {
        $normalized = $this->normalizeKeyword($value);

        foreach ($groups as $key => $variants) {
            foreach ($variants as $variant) {
                if ($this->normalizeKeyword($variant) === $normalized) {
                    return $key;
                }
            }
        }

        return null;
    }

    private function rawValuesForKeys(array $keys, array $groups): array
    {
        $values = [];

        foreach ($keys as $key) {
            foreach ($groups[$key] ?? [] as $value) {
                if ($value === '') {
                    continue;
                }

                $values[$value] = $value;
            }
        }

        return array_values($values);
    }

    private function normalizedValuesForKeys(array $keys, array $groups): array
    {
        $values = [];

        foreach ($keys as $key) {
            foreach ($groups[$key] ?? [] as $value) {
                $normalized = $this->normalizeKeyword($value);

                if ($normalized === '') {
                    continue;
                }

                $values[$normalized] = $normalized;
            }
        }

        return array_values($values);
    }

    private function applyKeywordFilter(Builder $builder, string $column, array $keys, array $groups): void
    {
        $rawValues = $this->rawValuesForKeys($keys, $groups);
        $values = $this->normalizedValuesForKeys($keys, $groups);

        if ($rawValues === [] && $values === []) {
            return;
        }

        $builder->where(function (Builder $keywordBuilder) use ($column, $rawValues, $values): void {
            $isFirstCondition = true;

            foreach ($rawValues as $value) {
                if ($isFirstCondition) {
                    $keywordBuilder->where($column, $value);
                    $isFirstCondition = false;

                    continue;
                }

                $keywordBuilder->orWhere($column, $value);
            }

            foreach ($values as $value) {
                if ($isFirstCondition) {
                    $keywordBuilder->whereRaw('LOWER(TRIM('.$column.')) = ?', [$value]);
                    $isFirstCondition = false;

                    continue;
                }

                $keywordBuilder->orWhereRaw('LOWER(TRIM('.$column.')) = ?', [$value]);
            }
        });
    }

    private function normalizeKeyword(string $value): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($value));

        return is_string($normalized) ? mb_strtolower($normalized) : '';
    }
}
