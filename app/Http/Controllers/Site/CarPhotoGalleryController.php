<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class CarPhotoGalleryController extends Controller
{
    public function index(): View
    {
        $carsWithPhotos = SiteCache::remember('cars-photo:list:all', static fn () => Car::query()
            ->with(['brand:id,name,slug'])
            ->whereHas('brand')
            ->whereHas('photos', static fn ($query) => $query
                ->whereNotNull('photo_path')
                ->where('photo_path', '!=', ''))
            ->latest()
            ->limit(30)
            ->get());

        return view('site.cars-photo', [
            'photoBrands' => $this->photoBrands(),
            'carsWithPhotos' => $carsWithPhotos,
            'selectedPhotoBrand' => null,
        ]);
    }

    public function brand(Brand $brand): View
    {
        $carsWithPhotos = SiteCache::remember("cars-photo:list:brand:{$brand->id}", static fn () => Car::query()
            ->with(['brand:id,name,slug'])
            ->whereHas('brand')
            ->where('brand_id', $brand->id)
            ->whereHas('photos', static fn ($query) => $query
                ->whereNotNull('photo_path')
                ->where('photo_path', '!=', ''))
            ->latest()
            ->get());

        return view('site.cars-photo', [
            'photoBrands' => $this->photoBrands(),
            'carsWithPhotos' => $carsWithPhotos,
            'selectedPhotoBrand' => $brand,
        ]);
    }

    private function photoBrands()
    {
        return SiteCache::remember('cars-photo:brands', static fn () => Brand::query()
            ->select(['id', 'name', 'slug'])
            ->whereHas('cars', static fn ($query) => $query
                ->whereHas('photos', static fn ($photoQuery) => $photoQuery
                    ->whereNotNull('photo_path')
                    ->where('photo_path', '!=', '')))
            ->orderBy('name')
            ->get());
    }
}
