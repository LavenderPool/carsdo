<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarCatalogRequest;
use App\Http\Requests\Admin\UpdateCarCatalogRequest;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarCatalog;
use App\Models\CarConfiguration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CarCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');

        $catalogs = CarCatalog::query()
            ->withCount('cars')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && in_array($status, ['published', 'draft'], true), function ($query) use ($status): void {
                $query->where('is_published', $status === 'published');
            })
            ->orderBy('sort_order')
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (CarCatalog $catalog): array => [
                'id' => $catalog->id,
                'name' => $catalog->name,
                'slug' => $catalog->slug,
                'is_published' => $catalog->is_published,
                'sort_order' => $catalog->sort_order,
                'cars_count' => $catalog->cars_count,
                'updated_at' => $catalog->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/CarCatalogs/Index', [
            'catalogs' => $catalogs,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/CarCatalogs/Create', [
            'options' => $this->options(),
        ]);
    }

    public function store(StoreCarCatalogRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $catalog = CarCatalog::query()->create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'filters_json' => $this->filtersPayload($validated),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'seo_h1' => $validated['seo_h1'] ?? null,
            'seo_og_image' => $validated['seo_og_image'] ?? null,
            'seo_canonical_url' => $validated['seo_canonical_url'] ?? null,
            'seo_robots' => $validated['seo_robots'] ?? null,
        ]);

        $this->syncManualCars($catalog, $validated['manual_cars'] ?? []);

        return redirect()
            ->route('admin.car-catalogs.index')
            ->with('success', 'Каталог добавлен.');
    }

    public function edit(CarCatalog $carCatalog): Response
    {
        $carCatalog->load([
            'cars' => fn ($query) => $query
                ->select('cars.id', 'cars.name', 'cars.brand_id')
                ->with(['brand:id,name'])
                ->orderBy('car_catalog_car.sort_order')
                ->orderBy('cars.name'),
        ]);

        $filters = is_array($carCatalog->filters_json) ? $carCatalog->filters_json : [];

        return Inertia::render('Admin/CarCatalogs/Edit', [
            'catalog' => [
                'id' => $carCatalog->id,
                'name' => $carCatalog->name,
                'slug' => $carCatalog->slug,
                'description' => $carCatalog->description,
                'is_published' => $carCatalog->is_published,
                'sort_order' => $carCatalog->sort_order,
                'price_min' => $filters['price_min'] ?? null,
                'price_max' => $filters['price_max'] ?? null,
                'year_from' => $filters['year_from'] ?? null,
                'year_to' => $filters['year_to'] ?? null,
                'is_electric_car' => $filters['is_electric_car'] ?? null,
                'brand_ids' => $filters['brand_ids'] ?? [],
                'drive_types' => $filters['drive_types'] ?? [],
                'engine_types' => $filters['engine_types'] ?? [],
                'manual_cars' => $carCatalog->cars->map(fn (Car $car): array => [
                    'car_id' => $car->id,
                    'sort_order' => (int) ($car->pivot->sort_order ?? 0),
                ])->values()->all(),
                'seo_title' => $carCatalog->seo_title,
                'seo_description' => $carCatalog->seo_description,
                'seo_h1' => $carCatalog->seo_h1,
                'seo_og_image' => $carCatalog->seo_og_image,
                'seo_canonical_url' => $carCatalog->seo_canonical_url,
                'seo_robots' => $carCatalog->seo_robots,
            ],
            'options' => $this->options(),
        ]);
    }

    public function update(UpdateCarCatalogRequest $request, CarCatalog $carCatalog): RedirectResponse
    {
        $validated = $request->validated();

        $carCatalog->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'filters_json' => $this->filtersPayload($validated),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'seo_h1' => $validated['seo_h1'] ?? null,
            'seo_og_image' => $validated['seo_og_image'] ?? null,
            'seo_canonical_url' => $validated['seo_canonical_url'] ?? null,
            'seo_robots' => $validated['seo_robots'] ?? null,
        ]);

        $this->syncManualCars($carCatalog, $validated['manual_cars'] ?? []);

        return redirect()
            ->route('admin.car-catalogs.edit', $carCatalog->id)
            ->with('success', 'Каталог обновлен.');
    }

    public function destroy(CarCatalog $carCatalog): RedirectResponse
    {
        CarCatalog::query()->whereKey($carCatalog->id)->delete();

        return redirect()
            ->route('admin.car-catalogs.index')
            ->with('success', 'Каталог удален.');
    }

    /**
     * @return array{brands: array<int, array{id: int, name: string}>, cars: array<int, array{id: int, label: string}>, drive_types: array<int, string>, engine_types: array<int, string>}
     */
    private function options(): array
    {
        $brands = Brand::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(fn (Brand $brand): array => [
                'id' => $brand->id,
                'name' => $brand->name,
            ])
            ->all();

        $cars = Car::query()
            ->with(['brand:id,name'])
            ->whereHas('brand')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'brand_id'])
            ->map(fn (Car $car): array => [
                'id' => $car->id,
                'label' => trim(($car->brand?->name ? $car->brand->name.' ' : '').$car->name),
            ])
            ->all();

        $driveTypes = CarConfiguration::query()
            ->whereNotNull('drive_type', 'and')
            ->where('drive_type', '!=', '')
            ->distinct()
            ->orderBy('drive_type', 'asc')
            ->pluck('drive_type')
            ->values()
            ->all();

        $engineTypes = CarConfiguration::query()
            ->whereNotNull('engine_type', 'and')
            ->where('engine_type', '!=', '')
            ->distinct()
            ->orderBy('engine_type', 'asc')
            ->pluck('engine_type')
            ->values()
            ->all();

        return [
            'brands' => $brands,
            'cars' => $cars,
            'drive_types' => $driveTypes,
            'engine_types' => $engineTypes,
        ];
    }

    /**
     * @param  array<int, array{car_id: int, sort_order?: int|null}>  $manualCars
     */
    private function syncManualCars(CarCatalog $catalog, array $manualCars): void
    {
        $syncPayload = [];

        foreach ($manualCars as $item) {
            $carId = (int) ($item['car_id'] ?? 0);

            if ($carId <= 0) {
                continue;
            }

            $syncPayload[$carId] = [
                'sort_order' => (int) ($item['sort_order'] ?? 0),
            ];
        }

        $catalog->cars()->sync($syncPayload);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function filtersPayload(array $validated): array
    {
        return [
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'year_from' => $validated['year_from'] ?? null,
            'year_to' => $validated['year_to'] ?? null,
            'is_electric_car' => $validated['is_electric_car'] ?? null,
            'brand_ids' => $validated['brand_ids'] ?? [],
            'drive_types' => $validated['drive_types'] ?? [],
            'engine_types' => $validated['engine_types'] ?? [],
        ];
    }
}
