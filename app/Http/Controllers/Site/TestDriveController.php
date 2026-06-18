<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarTestDrive;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class TestDriveController extends Controller
{
    public function index(): View
    {
        $testDrives = SiteCache::remember('test-drive:list:all', static function () {
            $latestTestDriveIds = CarTestDrive::query()
                ->selectRaw('MAX(id)', [])
                ->groupBy('car_id');

            return CarTestDrive::query()
                ->with(['car.brand:id,name,slug'])
                ->whereIn('id', $latestTestDriveIds)
                ->whereHas('car.brand')
                ->latest()
                ->limit(24)
                ->get();
        });

        return view('site.test-drives', [
            'testDriveBrands' => $this->testDriveBrands(),
            'testDrives' => $testDrives,
            'selectedTestDriveBrand' => null,
            'isElectricOnly' => false,
        ]);
    }

    public function electric(): View
    {
        $testDrives = SiteCache::remember('test-drive:list:electric', static function () {
            $latestTestDriveIds = CarTestDrive::query()
                ->selectRaw('MAX(id)', [])
                ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
                ->groupBy('car_id');

            return CarTestDrive::query()
                ->with(['car.brand:id,name,slug'])
                ->whereIn('id', $latestTestDriveIds)
                ->whereHas('car.brand')
                ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
                ->latest()
                ->limit(24)
                ->get();
        });

        return view('site.test-drives', [
            'testDriveBrands' => $this->testDriveBrands(),
            'testDrives' => $testDrives,
            'selectedTestDriveBrand' => null,
            'isElectricOnly' => true,
        ]);
    }

    public function brand(Brand $brand): View
    {
        $testDrives = SiteCache::remember("test-drive:list:brand:{$brand->id}", static function () use ($brand) {
            $latestTestDriveIds = CarTestDrive::query()
                ->selectRaw('MAX(id)', [])
                ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
                ->groupBy('car_id');

            return CarTestDrive::query()
                ->with(['car.brand:id,name,slug'])
                ->whereIn('id', $latestTestDriveIds)
                ->whereHas('car.brand')
                ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
                ->latest()
                ->get();
        });

        return view('site.test-drives', [
            'testDriveBrands' => $this->testDriveBrands(),
            'testDrives' => $testDrives,
            'selectedTestDriveBrand' => $brand,
            'isElectricOnly' => false,
        ]);
    }

    private function testDriveBrands()
    {
        return SiteCache::remember('test-drive:brands', static fn () => Brand::query()
            ->select(['id', 'name', 'slug'])
            ->whereHas('cars.testDrives')
            ->orderBy('name')
            ->get());
    }
}
