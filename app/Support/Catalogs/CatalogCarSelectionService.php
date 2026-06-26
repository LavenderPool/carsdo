<?php

namespace App\Support\Catalogs;

use App\Models\Car;
use App\Models\CarCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;

class CatalogCarSelectionService
{
    public function paginate(CarCatalog $catalog, int $perPage = 30, ?int $page = null): LengthAwarePaginator
    {
        $page = max(1, $page ?? (int) request()->integer('page', 1));
        $orderedIds = $this->resolvedCarIds($catalog);
        $total = count($orderedIds);

        if ($total === 0) {
            return new Paginator([], 0, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $slice = array_slice($orderedIds, ($page - 1) * $perPage, $perPage);

        $positions = array_flip($slice);

        $cars = Car::query()
            ->with([
                'brand:id,name,slug',
                'configurations:id,car_id,price,currency',
            ])
            ->whereIn('id', $slice)
            ->get()
            ->sortBy(fn (Car $car): int => $positions[$car->id] ?? PHP_INT_MAX)
            ->values();

        return new Paginator($cars, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    public function resolvedCarIds(CarCatalog $catalog): array
    {
        $manualIds = $catalog->cars()
            ->select('cars.id')
            ->whereHas('brand')
            ->orderByPivot('sort_order')
            ->orderBy('cars.name')
            ->pluck('cars.id')
            ->all();

        $autoIds = $this->autoQuery($catalog)
            ->select('cars.id')
            ->pluck('cars.id')
            ->all();

        return array_values(array_unique([...$manualIds, ...$autoIds]));
    }

    private function autoQuery(CarCatalog $catalog): Builder
    {
        $filters = $catalog->filters_json ?? [];
        $query = Car::query()
            ->whereHas('brand')
            ->where('is_soon', false);

        $brandIds = collect(Arr::wrap($filters['brand_ids'] ?? []))
            ->filter(fn (mixed $value): bool => is_numeric($value))
            ->map(fn (mixed $value): int => (int) $value)
            ->values()
            ->all();

        if ($brandIds !== []) {
            $query->whereIn('brand_id', $brandIds);
        }

        if (array_key_exists('is_electric_car', $filters) && $filters['is_electric_car'] !== null) {
            $query->where('is_electric_car', (bool) $filters['is_electric_car']);
        }

        $yearFrom = is_numeric($filters['year_from'] ?? null) ? (int) $filters['year_from'] : null;
        $yearTo = is_numeric($filters['year_to'] ?? null) ? (int) $filters['year_to'] : null;

        if ($yearFrom !== null) {
            $query->where('year', '>=', (string) $yearFrom);
        }

        if ($yearTo !== null) {
            $query->where('year', '<=', (string) $yearTo);
        }

        $priceMin = is_numeric($filters['price_min'] ?? null) ? (int) $filters['price_min'] : null;
        $priceMax = is_numeric($filters['price_max'] ?? null) ? (int) $filters['price_max'] : null;

        if ($priceMin !== null || $priceMax !== null) {
            $query->where(function (Builder $priceQuery) use ($priceMin, $priceMax): void {
                $priceQuery->whereHas('configurations', function (Builder $configurationQuery) use ($priceMin, $priceMax): void {
                    if ($priceMin !== null) {
                        $configurationQuery->where('price', '>=', $priceMin);
                    }

                    if ($priceMax !== null) {
                        $configurationQuery->where('price', '<=', $priceMax);
                    }
                })->orWhere(function (Builder $carPriceQuery) use ($priceMin, $priceMax): void {
                    if ($priceMin !== null) {
                        $carPriceQuery->whereRaw('CAST(start_price AS UNSIGNED) >= ?', [$priceMin]);
                    }

                    if ($priceMax !== null) {
                        $carPriceQuery->whereRaw('CAST(end_price AS UNSIGNED) <= ?', [$priceMax]);
                    }
                });
            });
        }

        $driveTypes = collect(Arr::wrap($filters['drive_types'] ?? []))
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->values()
            ->all();
        if ($driveTypes !== []) {
            $query->whereHas('configurations', fn (Builder $configurationQuery) => $configurationQuery->whereIn('drive_type', $driveTypes));
        }

        $engineTypes = collect(Arr::wrap($filters['engine_types'] ?? []))
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->values()
            ->all();
        if ($engineTypes !== []) {
            $query->whereHas('configurations', fn (Builder $configurationQuery) => $configurationQuery->whereIn('engine_type', $engineTypes));
        }

        return $query
            ->orderBy('name')
            ->orderBy('id');
    }
}
