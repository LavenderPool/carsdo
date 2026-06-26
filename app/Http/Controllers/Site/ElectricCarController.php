<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ElectricCarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $page = max(1, (int) $request->integer('page', 1));

        $electricCars = SiteCache::remember("electric-cars:page:{$page}:v2", static fn () => Car::query()
            ->with([
                'brand:id,name,slug',
                'configurations:id,car_id,price,currency',
            ])
            ->whereHas('brand')
            ->where('is_electric_car', true)
            ->where('is_soon', false)
            ->orderBy('name')
            ->paginate(30));

        $soonElectricCars = $electricCars->currentPage() === 1
            ? SiteCache::remember('electric-cars:soon:v2', static fn () => Car::query()
                ->with([
                    'brand:id,name,slug',
                    'configurations:id,car_id,price,currency',
                ])
                ->whereHas('brand')
                ->where('is_electric_car', true)
                ->where('is_soon', true)
                ->orderBy('name')
                ->get())
            : new Collection();

        return view('site.electric-cars', [
            'electricCars' => $electricCars,
            'soonElectricCars' => $soonElectricCars,
        ]);
    }
}
