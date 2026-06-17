<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarConfigurationController;
use App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController;
use App\Http\Controllers\Admin\CarConfigurationEquipmentController;
use App\Http\Controllers\Admin\CarConfigurationGroupController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarCrashTestController;
use App\Http\Controllers\Admin\CarPhotoController;
use App\Http\Controllers\Admin\CarPhotoGroupController;
use App\Http\Controllers\Admin\CarReviewController;
use App\Http\Controllers\Admin\CarTestDriveController;
use App\Http\Controllers\Admin\DangerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarCrashTest;
use App\Models\CarTestDrive;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

$rememberView = static function (string $key): bool {
    $cookieName = 'recent_views_v1';
    $maxRecentViews = 50;
    $cookieLifetimeMinutes = 60 * 24 * 365;

    $rawRecentViews = request()->cookie($cookieName);
    $recentViews = [];

    if (is_string($rawRecentViews) && $rawRecentViews !== '') {
        $decodedRecentViews = json_decode($rawRecentViews, true);

        if (is_array($decodedRecentViews)) {
            $recentViews = array_values(array_filter(
                $decodedRecentViews,
                static fn (mixed $value): bool => is_string($value) && $value !== '',
            ));
        }
    }

    $alreadyCounted = in_array($key, $recentViews, true);

    $recentViews = array_values(array_filter(
        $recentViews,
        static fn (string $value): bool => $value !== $key,
    ));
    array_unshift($recentViews, $key);
    $recentViews = array_slice($recentViews, 0, $maxRecentViews);

    Cookie::queue(cookie(
        $cookieName,
        json_encode($recentViews, JSON_UNESCAPED_SLASHES),
        $cookieLifetimeMinutes,
    ));

    return ! $alreadyCounted;
};

Route::get('/', function () {
    $newCars = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->latest()
        ->limit(12)
        ->get();

    $soonCars = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->where('is_soon', true)
        ->latest()
        ->limit(12)
        ->get();

    $crashTests = CarCrashTest::query()
        ->with(['car.brand:id,name,slug'])
        ->whereHas('car.brand')
        ->latest()
        ->limit(12)
        ->get();

    $latestTestDriveIds = CarTestDrive::query()
        ->selectRaw('MAX(id)', [])
        ->groupBy('car_id');

    $testDrives = CarTestDrive::query()
        ->with(['car.brand:id,name,slug'])
        ->whereIn('id', $latestTestDriveIds)
        ->whereHas('car.brand')
        ->latest()
        ->limit(12)
        ->get();

    $popularCars = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->popular()
        ->limit(40)
        ->get();

    return view('site.main', compact(
        'newCars',
        'soonCars',
        'crashTests',
        'testDrives',
        'popularCars',
    ));
});



Route::middleware(['auth', HandleInertiaRequests::class])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('brands', BrandController::class)
            ->except(['show']);
        Route::resource('cars', CarController::class)
            ->except(['show']);
        Route::resource('cars.crash-tests', CarCrashTestController::class)
            ->parameters(['crash-tests' => 'crashTest'])
            ->except(['show']);
        Route::resource('cars.test-drives', CarTestDriveController::class)
            ->parameters(['test-drives' => 'testDrive'])
            ->except(['show']);
        Route::resource('cars.reviews', CarReviewController::class)
            ->parameters(['reviews' => 'review'])
            ->except(['show']);
        Route::resource('cars.configuration-groups', CarConfigurationGroupController::class)
            ->parameters(['configuration-groups' => 'configurationGroup'])
            ->except(['show']);
        Route::resource('cars.configurations', CarConfigurationController::class)
            ->parameters(['configurations' => 'configuration'])
            ->except(['show']);
        Route::resource('cars.equipment-categories', CarConfigurationEquipmentCategoryController::class)
            ->parameters(['equipment-categories' => 'equipmentCategory'])
            ->except(['show']);
        Route::resource('cars.equipment', CarConfigurationEquipmentController::class)
            ->parameters(['equipment' => 'equipment'])
            ->except(['show']);
        Route::resource('cars.photo-groups', CarPhotoGroupController::class)
            ->parameters(['photo-groups' => 'photoGroup'])
            ->except(['show']);
        Route::resource('cars.photos', CarPhotoController::class)
            ->parameters(['photos' => 'photo'])
            ->except(['show']);
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
        Route::get('/import/{importRun}', [ImportController::class, 'status'])->name('import.status');
        Route::post('/import/{importRun}/stop', [ImportController::class, 'stop'])->name('import.stop');
        Route::get('/danger/full-clear', [DangerController::class, 'fullClear'])->name('danger.full-clear');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/covers/{brand_slug}/{car_slug}/cover.jpg', function (string $brand_slug, string $car_slug) {
    $coverPath = storage_path("app/public/covers/{$brand_slug}/{$car_slug}/cover.jpg");

    abort_unless(is_file($coverPath), 404);

    return response()->file($coverPath);
})->where([
    'brand_slug' => '[A-Za-z0-9\-]+',
    'car_slug' => '[A-Za-z0-9\-]+',
]);

Route::get('/crash-test/', function () {
    $crashTestBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.crashTest')
        ->orderBy('name')
        ->get();

    $crashTests = CarCrashTest::query()
        ->with(['car.brand:id,name,slug'])
        ->whereHas('car.brand')
        ->latest()
        ->limit(24)
        ->get();

    return view('site.crash-trests', [
        'crashTestBrands' => $crashTestBrands,
        'crashTests' => $crashTests,
        'selectedCrashTestBrand' => null,
        'isElectricOnly' => false,
    ]);
});

Route::get('/crash-test/electric-cars/', function () {
    $crashTestBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.crashTest')
        ->orderBy('name')
        ->get();

    $crashTests = CarCrashTest::query()
        ->with(['car.brand:id,name,slug'])
        ->whereHas('car.brand')
        ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
        ->latest()
        ->limit(24)
        ->get();

    return view('site.crash-trests', [
        'crashTestBrands' => $crashTestBrands,
        'crashTests' => $crashTests,
        'selectedCrashTestBrand' => null,
        'isElectricOnly' => true,
    ]);
});

Route::get('/crash-test/{brand:slug}/', function (Brand $brand) {
    $crashTestBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.crashTest')
        ->orderBy('name')
        ->get();

    $crashTests = CarCrashTest::query()
        ->with(['car.brand:id,name,slug'])
        ->whereHas('car.brand')
        ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
        ->latest()
        ->get();

    return view('site.crash-trests', [
        'crashTestBrands' => $crashTestBrands,
        'crashTests' => $crashTests,
        'selectedCrashTestBrand' => $brand,
        'isElectricOnly' => false,
    ]);
});

Route::get('/test-drive/', function () {
    $testDriveBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.testDrives')
        ->orderBy('name')
        ->get();

    $latestTestDriveIds = CarTestDrive::query()
        ->selectRaw('MAX(id)', [])
        ->groupBy('car_id');

    $testDrives = CarTestDrive::query()
        ->with(['car.brand:id,name,slug'])
        ->whereIn('id', $latestTestDriveIds)
        ->whereHas('car.brand')
        ->latest()
        ->limit(24)
        ->get();

    return view('site.test-drives', [
        'testDriveBrands' => $testDriveBrands,
        'testDrives' => $testDrives,
        'selectedTestDriveBrand' => null,
        'isElectricOnly' => false,
    ]);
});

Route::get('/test-drive/electric-cars/', function () {
    $testDriveBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.testDrives')
        ->orderBy('name')
        ->get();

    $latestTestDriveIds = CarTestDrive::query()
        ->selectRaw('MAX(id)', [])
        ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
        ->groupBy('car_id');

    $testDrives = CarTestDrive::query()
        ->with(['car.brand:id,name,slug'])
        ->whereIn('id', $latestTestDriveIds)
        ->whereHas('car.brand')
        ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
        ->latest()
        ->limit(24)
        ->get();

    return view('site.test-drives', [
        'testDriveBrands' => $testDriveBrands,
        'testDrives' => $testDrives,
        'selectedTestDriveBrand' => null,
        'isElectricOnly' => true,
    ]);
});

Route::get('/test-drive/{brand:slug}/', function (Brand $brand) {
    $testDriveBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars.testDrives')
        ->orderBy('name')
        ->get();

    $latestTestDriveIds = CarTestDrive::query()
        ->selectRaw('MAX(id)', [])
        ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
        ->groupBy('car_id');

    $testDrives = CarTestDrive::query()
        ->with(['car.brand:id,name,slug'])
        ->whereIn('id', $latestTestDriveIds)
        ->whereHas('car.brand')
        ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
        ->latest()
        ->get();

    return view('site.test-drives', [
        'testDriveBrands' => $testDriveBrands,
        'testDrives' => $testDrives,
        'selectedTestDriveBrand' => $brand,
        'isElectricOnly' => false,
    ]);
});

Route::get('/new-cars-{year}/', function (string $year) {
    $displayYear = (int) $year;
    $hasCarsForYear = static fn (int $candidateYear): bool => Car::query()
        ->whereHas('brand')
        ->where('year', (string) $candidateYear)
        ->where('is_soon', false)
        ->exists();

    $navigationYears = [];

    if ($hasCarsForYear($displayYear + 1)) {
        $navigationYears[] = $displayYear + 1;
    }

    if ($hasCarsForYear($displayYear - 1)) {
        $navigationYears[] = $displayYear - 1;
    } elseif ($hasCarsForYear($displayYear + 2)) {
        $navigationYears[] = $displayYear + 2;
    }

    $navigationYears = array_values(array_unique($navigationYears));

    $newCars = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->where('year', $year)
        ->where('is_soon', false)
        ->orderBy('name')
        ->paginate(30);

    return view('site.new-cars', [
        'year' => $year,
        'newCars' => $newCars,
        'navigationYears' => $navigationYears,
    ]);
})->where([
    'year' => '20[0-9]{2}',
]);

Route::get('/electric-cars/', function () {
    $electricCars = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->where('is_electric_car', true)
        ->where('is_soon', false)
        ->orderBy('name')
        ->paginate(30);

    $soonElectricCars = collect();

    if ($electricCars->currentPage() === 1) {
        $soonElectricCars = Car::query()
            ->with(['brand:id,name,slug'])
            ->whereHas('brand')
            ->where('is_electric_car', true)
            ->where('is_soon', true)
            ->orderBy('name')
            ->get();
    }

    return view('site.electric-cars', [
        'electricCars' => $electricCars,
        'soonElectricCars' => $soonElectricCars,
    ]);
});

Route::get('/cars-photo/', function () {
    $photoBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars', static function ($query) {
            $query->whereHas('photos', static function ($photoQuery) {
                $photoQuery
                    ->whereNotNull('photo_path')
                    ->where('photo_path', '!=', '');
            });
        })
        ->orderBy('name')
        ->get();

    $carsWithPhotos = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->whereHas('photos', static function ($query) {
            $query
                ->whereNotNull('photo_path')
                ->where('photo_path', '!=', '');
        })
        ->latest()
        ->limit(30)
        ->get();

    return view('site.cars-photo', [
        'photoBrands' => $photoBrands,
        'carsWithPhotos' => $carsWithPhotos,
        'selectedPhotoBrand' => null,
    ]);
});

Route::get('/cars-photo/{brand:slug}/', function (Brand $brand) {
    $photoBrands = Brand::query()
        ->select(['id', 'name', 'slug'])
        ->whereHas('cars', static function ($query) {
            $query->whereHas('photos', static function ($photoQuery) {
                $photoQuery
                    ->whereNotNull('photo_path')
                    ->where('photo_path', '!=', '');
            });
        })
        ->orderBy('name')
        ->get();

    $carsWithPhotos = Car::query()
        ->with(['brand:id,name,slug'])
        ->whereHas('brand')
        ->where('brand_id', $brand->id)
        ->whereHas('photos', static function ($query) {
            $query
                ->whereNotNull('photo_path')
                ->where('photo_path', '!=', '');
        })
        ->latest()
        ->get();

    return view('site.cars-photo', [
        'photoBrands' => $photoBrands,
        'carsWithPhotos' => $carsWithPhotos,
        'selectedPhotoBrand' => $brand,
    ]);
});


Route::get('/{brand:slug}', function (Brand $brand) use ($rememberView) {
    if ($rememberView("brand:{$brand->id}")) {
        $brand->increment('views_count', 1, []);
    }

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'end_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    $currentCars = $brand->cars
        ->where('is_soon', false)
        ->where('is_another_models', false)
        ->values();
    $soonCars = $brand->cars
        ->where('is_soon', true)
        ->values();
    $otherCars = $brand->cars
        ->where('is_another_models', true)
        ->values();

    return view('site.brand', [
        'brand' => $brand,
        'currentYear' => now()->year,
        'currentCars' => $currentCars,
        'soonCars' => $soonCars,
        'otherCars' => $otherCars,
    ]);
});

Route::get('/{brand:slug}/{car:slug}/test-drive/', function (Brand $brand, Car $car) {
    abort_if($car->brand_id !== $brand->id, 404);

    $car->load([
        'brand:id,name,slug',
        'testDrives:id,car_id,import_index,author,video_path',
        'configurationGroups:id,car_id,name,order,import_index',
        'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
        'reviews:id,car_id,import_index,type,value',
        'crashTest:id,car_id,year,rating,video_path',
    ]);

    abort_unless($car->testDrives->isNotEmpty(), 404);

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    return view('site.car.test-drive', [
        'brand' => $brand,
        'car' => $car,
    ]);
});

Route::get('/{brand:slug}/{car:slug}/crash-test/', function (Brand $brand, Car $car) {
    abort_if($car->brand_id !== $brand->id, 404);

    $car->load([
        'brand:id,name,slug',
        'crashTest:id,car_id,year,rating,video_path',
        'testDrives:id,car_id,import_index,author,video_path',
        'configurationGroups:id,car_id,name,order,import_index',
        'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
        'reviews:id,car_id,import_index,type,value',
    ]);

    abort_unless($car->crashTest !== null, 404);

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    return view('site.car.crash-test', [
        'brand' => $brand,
        'car' => $car,
    ]);
});

Route::get('/{brand:slug}/{car:slug}/reviews/', function (Brand $brand, Car $car) {
    abort_if($car->brand_id !== $brand->id, 404);

    $car->load([
        'brand:id,name,slug',
        'reviews:id,car_id,import_index,type,value',
        'configurationGroups:id,car_id,name,order,import_index',
        'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
    ]);

    abort_unless($car->reviews->isNotEmpty(), 404);

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    return view('site.car.reviews', [
        'brand' => $brand,
        'car' => $car,
    ]);
});

Route::get('/{brand:slug}/{car:slug}/equipment-{order}/', function (Brand $brand, Car $car, int $order) {
    abort_if($car->brand_id !== $brand->id, 404);

    $car->load([
        'brand:id,name,slug',
        'crashTest:id,car_id,year,rating,video_path',
        'testDrives:id,car_id,import_index,author,video_path',
        'reviews:id,car_id,import_index,type,value',
        'configurationGroups:id,car_id,name,order,import_index',
        'configurationGroups.equipmentCategories:id,car_configuration_group_id,name,import_index',
        'configurationGroups.equipmentCategories.items:id,car_configuration_equipment_category_id,import_index,value,is_extension,price',
        'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
        'photoGroups:id,car_id,name',
        'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
        'photos:id,car_id,car_photo_group_id,photo_path',
    ]);

    $configurationGroups = $car->configurationGroups
        ->sortBy([
            ['order', 'asc'],
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();

    $selectedGroup = $configurationGroups->get($order - 1);
    abort_if($selectedGroup === null, 404);

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    return view('site.car.equipment', [
        'brand' => $brand,
        'car' => $car,
        'selectedGroup' => $selectedGroup,
        'selectedOrder' => $order,
    ]);
})->where('order', '[1-9]\d*');

Route::get('/{brand:slug}/{car:slug}', function (Brand $brand, Car $car) use ($rememberView) {
    abort_if($car->brand_id !== $brand->id, 404);

    if ($rememberView("car:{$car->id}")) {
        $car->increment('views_count', 1, []);
    }

    $car->load([
        'brand:id,name,slug',
        'crashTest:car_id,year,rating,video_path',
        'testDrives:id,car_id,import_index,author,video_path',
        'reviews:id,car_id,import_index,type,value',
        'configurationGroups:id,car_id,name,order,import_index',
        'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
        'photoGroups:id,car_id,name',
        'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
        'photos:id,car_id,car_photo_group_id,photo_path',
    ]);

    $brand->load([
        'cars' => fn ($query) => $query
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name'),
    ]);

    return view('site.car.index', [
        'brand' => $brand,
        'car' => $car,
    ]);
});