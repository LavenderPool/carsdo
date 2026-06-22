<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Support\Cache\SiteCache;
use App\Support\RecentViews;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'count');
        if (!in_array($sort, ['count', 'alphabet'], true)) {
            $sort = 'count';
        }

        $brands = SiteCache::remember('brands:index', static function () {
            return Brand::query()
                ->select(['id', 'name', 'slug'])
                ->withCount('cars')
                ->orderBy('name')
                ->get();
        });

        $brands = $brands
            ->sort(function ($left, $right) use ($sort): int {
                if ($sort === 'alphabet') {
                    return strcasecmp($left->name, $right->name);
                }

                $countDiff = $right->cars_count <=> $left->cars_count;

                if ($countDiff !== 0) {
                    return $countDiff;
                }

                return strcasecmp($left->name, $right->name);
            })
            ->values();

        return view('site.brands', [
            'brands' => $brands,
            'sort' => $sort,
        ]);
    }

    public function show(Brand $brand, RecentViews $recentViews): View
    {
        if ($recentViews->remember("brand:{$brand->id}")) {
            $brand->incrementQuietly('views_count');
        }

        $groups = SiteCache::remember("brand:{$brand->id}:page", static function () use ($brand): array {
            $cars = $brand->cars()
                ->select('id', 'brand_id', 'name', 'slug', 'cover_path', 'start_price', 'end_price', 'is_soon', 'is_another_models', 'year', 'is_electric_car')
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
