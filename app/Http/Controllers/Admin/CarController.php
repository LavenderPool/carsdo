<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarRequest;
use App\Http\Requests\Admin\UpdateCarRequest;
use App\Models\Brand;
use App\Models\Car;
use App\Support\Media\CarMediaStorage;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $brandId = $request->integer('brand_id');
        $isSoon = $request->input('is_soon');
        $isElectric = $request->input('is_electric_car');

        $cars = Car::query()
            ->with('brand:id,name,slug')
            ->when($search !== '', fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%"))
            ->when($brandId > 0, fn ($query) => $query->where('brand_id', $brandId))
            ->when($isSoon !== null && $isSoon !== '', fn ($query) => $query->where('is_soon', filter_var($isSoon, FILTER_VALIDATE_BOOL)))
            ->when($isElectric !== null && $isElectric !== '', fn ($query) => $query->where('is_electric_car', filter_var($isElectric, FILTER_VALIDATE_BOOL)))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Car $car) => [
                'id' => $car->id,
                'name' => $car->name,
                'slug' => $car->slug,
                'brand' => $car->brand?->name,
                'year' => $car->year,
                'is_soon' => $car->is_soon,
                'is_electric_car' => $car->is_electric_car,
                'start_price' => $car->start_price,
                'end_price' => $car->end_price,
                'views_count' => $car->views_count,
                'created_at' => $car->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Cars/Index', [
            'cars' => $cars,
            'filters' => [
                'search' => $search,
                'brand_id' => $brandId > 0 ? $brandId : null,
                'is_soon' => $isSoon === '' ? null : $isSoon,
                'is_electric_car' => $isElectric === '' ? null : $isElectric,
            ],
            'brands' => Brand::query()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Cars/Create', [
            'brands' => Brand::query()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request): RedirectResponse
    {
        Car::query()->create($request->validated());

        return redirect()
            ->route('admin.cars.index')
            ->with('success', 'Автомобиль создан.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Edit', [
            'car' => array_merge([
                'id' => $car->id,
                'brand_id' => $car->brand_id,
                'name' => $car->name,
                'slug' => $car->slug,
                'year' => $car->year,
                'cover_path' => $car->cover_path,
                'start_price' => $car->start_price,
                'end_price' => $car->end_price,
                'official_site' => $car->official_site,
                'is_electric_car' => $car->is_electric_car,
                'is_soon' => $car->is_soon,
                'is_another_models' => $car->is_another_models,
            ], $car->only(AdminSeoFields::carFields())),
            'brands' => Brand::query()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
            'nestedLinks' => [
                'crash_test' => route('admin.cars.crash-tests.index', $car),
                'test_drives' => route('admin.cars.test-drives.index', $car),
                'reviews' => route('admin.cars.reviews.index', $car),
                'configuration_groups' => route('admin.cars.configuration-groups.index', $car),
                'configurations' => route('admin.cars.configurations.index', $car),
                'equipment_categories' => route('admin.cars.equipment-categories.index', $car),
                'equipment' => route('admin.cars.equipment.index', $car),
                'photo_groups' => route('admin.cars.photo-groups.index', $car),
                'photos' => route('admin.cars.photos.index', $car),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $car->update($request->validated());

        return redirect()
            ->route('admin.cars.index')
            ->with('success', 'Автомобиль обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car): RedirectResponse
    {
        DB::transaction(function () use ($car): void {
            $car->load('photos');
            $groupIds = $car->configurationGroups()->pluck('id');
            $categoryIds = $car->configurationGroups()
                ->with('equipmentCategories:id,car_configuration_group_id')
                ->get()
                ->flatMap(fn ($group) => $group->equipmentCategories->pluck('id'))
                ->all();

            DB::table('car_configuration_equipment')
                ->whereIn('car_configuration_equipment_category_id', $categoryIds)
                ->delete();

            DB::table('car_configuration_equipment_categories')
                ->whereIn('id', $categoryIds)
                ->delete();

            DB::table('car_configurations')
                ->whereIn('car_configuration_group_id', $groupIds)
                ->delete();

            DB::table('car_configuration_groups')
                ->where('car_id', $car->id)
                ->delete();

            DB::table('car_crash_tests')->where('car_id', $car->id)->delete();
            DB::table('car_test_drives')->where('car_id', $car->id)->delete();
            DB::table('car_reviews')->where('car_id', $car->id)->delete();
            DB::table('car_dealers')->where('car_id', $car->id)->delete();
            DB::table('car_photos')->where('car_id', $car->id)->delete();
            DB::table('car_photo_groups')->where('car_id', $car->id)->delete();

            Car::query()->whereKey($car->id)->delete();
        });

        CarMediaStorage::deletePhotoFiles($car->photos);
        CarMediaStorage::deleteCarDirectories($car);

        return redirect()
            ->route('admin.cars.index')
            ->with('success', 'Автомобиль удален.');
    }
}
