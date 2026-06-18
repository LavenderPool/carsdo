<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarCrashTest;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class CrashTestController extends Controller
{
    public function index(): View
    {
        $crashTests = SiteCache::remember('crash-test:list:all', static fn () => CarCrashTest::query()
            ->with(['car.brand:id,name,slug'])
            ->whereHas('car.brand')
            ->latest()
            ->limit(24)
            ->get());

        return view('site.crash-trests', [
            'crashTestBrands' => $this->crashTestBrands(),
            'crashTests' => $crashTests,
            'selectedCrashTestBrand' => null,
            'isElectricOnly' => false,
        ]);
    }

    public function electric(): View
    {
        $crashTests = SiteCache::remember('crash-test:list:electric', static fn () => CarCrashTest::query()
            ->with(['car.brand:id,name,slug'])
            ->whereHas('car.brand')
            ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
            ->latest()
            ->limit(24)
            ->get());

        return view('site.crash-trests', [
            'crashTestBrands' => $this->crashTestBrands(),
            'crashTests' => $crashTests,
            'selectedCrashTestBrand' => null,
            'isElectricOnly' => true,
        ]);
    }

    public function brand(Brand $brand): View
    {
        $crashTests = SiteCache::remember("crash-test:list:brand:{$brand->id}", static fn () => CarCrashTest::query()
            ->with(['car.brand:id,name,slug'])
            ->whereHas('car.brand')
            ->whereHas('car', static fn ($query) => $query->where('brand_id', $brand->id))
            ->latest()
            ->get());

        return view('site.crash-trests', [
            'crashTestBrands' => $this->crashTestBrands(),
            'crashTests' => $crashTests,
            'selectedCrashTestBrand' => $brand,
            'isElectricOnly' => false,
        ]);
    }

    private function crashTestBrands()
    {
        return SiteCache::remember('crash-test:brands', static fn () => Brand::query()
            ->select(['id', 'name', 'slug'])
            ->whereHas('cars.crashTest')
            ->orderBy('name')
            ->get());
    }
}
