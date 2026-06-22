<?php

namespace App\Services\Site;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchService
{
    private const MIN_QUERY_LENGTH = 2;

    private const DEFAULT_BRAND_SUGGEST_LIMIT = 6;

    private const DEFAULT_MODEL_SUGGEST_LIMIT = 8;

    private const DEFAULT_MODEL_PAGE_SIZE = 30;

    public function suggest(?string $rawQuery, int $brandLimit = self::DEFAULT_BRAND_SUGGEST_LIMIT, int $modelLimit = self::DEFAULT_MODEL_SUGGEST_LIMIT): array
    {
        $query = $this->normalizeQuery($rawQuery);

        if (! $this->canSearch($query)) {
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
            'models' => $this->carsQuery($query)
                ->limit(max(1, $modelLimit))
                ->get(),
        ];
    }

    public function search(?string $rawQuery, int $page = 1, int $perPage = self::DEFAULT_MODEL_PAGE_SIZE): array
    {
        $query = $this->normalizeQuery($rawQuery);
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        if (! $this->canSearch($query)) {
            return [
                'query' => $query,
                'brands' => collect(),
                'models' => $this->emptyPaginator($page, $perPage),
            ];
        }

        return [
            'query' => $query,
            'brands' => $this->brandsQuery($query)
                ->withCount('cars')
                ->get(),
            'models' => $this->carsQuery($query)
                ->paginate($perPage, ['*'], 'page', $page)
                ->withQueryString(),
        ];
    }

    public function minQueryLength(): int
    {
        return self::MIN_QUERY_LENGTH;
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

    private function carsQuery(string $query): Builder
    {
        $contains = '%'.$query.'%';
        $startsWith = $query.'%';

        return Car::query()
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
            ->with(['brand:id,name,slug'])
            ->whereHas('brand')
            ->where(function (Builder $builder) use ($contains): void {
                $builder
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
            )
            ->popular()
            ->orderBy('cars.name');
    }

    private function normalizeQuery(?string $query): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim((string) $query));

        return is_string($normalized) ? $normalized : '';
    }

    private function canSearch(string $query): bool
    {
        return $query !== '' && mb_strlen($query) >= self::MIN_QUERY_LENGTH;
    }

    private function emptyPaginator(int $page, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            new Collection(),
            0,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
