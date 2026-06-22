<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarDealerController;
use App\Http\Controllers\Admin\CarPageSeoController;
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
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Site\BrandController as SiteBrandController;
use App\Http\Controllers\Site\CarController as SiteCarController;
use App\Http\Controllers\Site\CarPhotoGalleryController;
use App\Http\Controllers\Site\CoverController;
use App\Http\Controllers\Site\CrashTestController;
use App\Http\Controllers\Site\ElectricCarController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\NewCarController;
use App\Http\Controllers\Site\PopularCarController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\TestDriveController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', HandleInertiaRequests::class])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('brands', BrandController::class)
            ->except(['show']);
        Route::resource('dealers', DealerController::class)
            ->except(['show']);
        Route::resource('car-dealers', CarDealerController::class)
            ->except(['show']);
        Route::resource('cars', CarController::class)
            ->except(['show']);
        Route::get('car-page-seos', [CarPageSeoController::class, 'index'])->name('car-page-seos.index');
        Route::get('car-page-seos/{pageKey}', [CarPageSeoController::class, 'edit'])->name('car-page-seos.edit');
        Route::put('car-page-seos/{pageKey}', [CarPageSeoController::class, 'update'])->name('car-page-seos.update');
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
        Route::get('/danger/set-local-ids', [DangerController::class, 'setLocalIds'])->name('danger.set-local-ids');
        Route::post('/danger/set-local-ids', [DangerController::class, 'applySetLocalIds'])->name('danger.set-local-ids.apply');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';


Route::get('/', HomeController::class)->name('home');

Route::get('/sitemap.xml', SitemapController::class);

Route::get('/covers/{brand_slug}/{car_slug}/cover.jpg', CoverController::class)
    ->where([
        'brand_slug' => '[A-Za-z0-9\-]+',
        'car_slug' => '[A-Za-z0-9\-]+',
    ]);

Route::get('/crash-test/', [CrashTestController::class, 'index'])->name('crash-test.index');
Route::get('/crash-test/electric-cars/', [CrashTestController::class, 'electric'])->name('crash-test.electric');
Route::get('/crash-test/{brand:slug}/', [CrashTestController::class, 'brand'])->name('crash-test.brand');

Route::get('/test-drive/', [TestDriveController::class, 'index'])->name('test-drive.index');
Route::get('/test-drive/electric-cars/', [TestDriveController::class, 'electric'])->name('test-drive.electric');
Route::get('/test-drive/{brand:slug}/', [TestDriveController::class, 'brand'])->name('test-drive.brand');

Route::get('/new-cars-{year}/', NewCarController::class)
    ->where(['year' => '20[0-9]{2}'])
    ->name('new-cars');

Route::get('/electric-cars/', ElectricCarController::class)->name('electric-cars');
Route::get('/popular-cars/', PopularCarController::class)->name('popular-cars');

Route::get('/cars-photo/', [CarPhotoGalleryController::class, 'index'])->name('cars-photo.index');
Route::get('/cars-photo/{brand:slug}/', [CarPhotoGalleryController::class, 'brand'])->name('cars-photo.brand');

Route::get('/brands/', [SiteBrandController::class, 'index'])->name('brands.index');
Route::get('/search/', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggest/', [SearchController::class, 'suggest'])->name('search.suggest');

Route::get('/{brand:slug}', [SiteBrandController::class, 'show'])->name('brand.show');

Route::get('/{brand:slug}/{car:slug}/test-drive/', [SiteCarController::class, 'testDrive'])->name('car.test-drive');
Route::get('/{brand:slug}/{car:slug}/crash-test/', [SiteCarController::class, 'crashTest'])->name('car.crash-test');
Route::get('/{brand:slug}/{car:slug}/reviews/', [SiteCarController::class, 'reviews'])->name('car.reviews');
Route::get('/{brand:slug}/{car:slug}/photo/', [SiteCarController::class, 'photo'])->name('car.photo');
Route::get('/{brand:slug}/{car:slug}/equipment-{localId}/', [SiteCarController::class, 'equipment'])
    ->where('localId', '\d+')
    ->name('car.equipment');
Route::get('/{brand:slug}/{car:slug}/{city:slug}', [SiteCarController::class, 'dealer'])
    ->name('car.dealer');
Route::get('/{brand:slug}/{car:slug}', [SiteCarController::class, 'show'])->name('car.show');
