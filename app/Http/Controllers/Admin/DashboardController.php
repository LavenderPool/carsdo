<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): Response
    {
        $topBrands = Brand::query()
            ->popular()
            ->limit(10)
            ->get(['id', 'name', 'slug', 'views_count']);

        $topCars = Car::query()
            ->with('brand:id,name,slug')
            ->whereHas('brand')
            ->popular()
            ->limit(10)
            ->get(['id', 'brand_id', 'name', 'slug', 'views_count']);

        return Inertia::render('Admin/Dashboard', [
            'brandsCount' => Brand::query()->count('*'),
            'carsCount' => Car::query()->count('*'),
            'topBrands' => $topBrands->map(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'views_count' => $brand->views_count,
            ])->values(),
            'topCars' => $topCars->map(fn (Car $car) => [
                'id' => $car->id,
                'name' => $car->name,
                'slug' => $car->slug,
                'brand' => $car->brand?->name,
                'brand_slug' => $car->brand?->slug,
                'views_count' => $car->views_count,
            ])->values(),
        ]);
    }
}
