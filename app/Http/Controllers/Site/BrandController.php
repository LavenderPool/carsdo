<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Support\Cache\SiteCache;
use App\Support\RecentViews;
use Illuminate\Contracts\View\View;

class BrandController extends Controller
{
    public function show(Brand $brand, RecentViews $recentViews): View
    {
        if ($recentViews->remember("brand:{$brand->id}")) {
            $brand->incrementQuietly('views_count');
        }

        $groups = SiteCache::remember("brand:{$brand->id}:page", static function () use ($brand): array {
            $cars = $brand->cars()
                ->select('id', 'brand_id', 'name', 'slug', 'cover_path', 'start_price', 'end_price', 'is_soon', 'is_another_models')
                ->orderBy('name')
                ->get();

            return [
                'currentCars' => $cars
                    ->where('is_soon', false)
                    ->where('is_another_models', false)
                    ->values(),
                'soonCars' => $cars
                    ->where('is_soon', true)
                    ->values(),
                'otherCars' => $cars
                    ->where('is_another_models', true)
                    ->values(),
            ];
        });

        return view('site.brand', [
            'brand' => $brand,
            'currentYear' => now()->year,
            'currentCars' => $groups['currentCars'],
            'soonCars' => $groups['soonCars'],
            'otherCars' => $groups['otherCars'],
        ]);
    }
}
