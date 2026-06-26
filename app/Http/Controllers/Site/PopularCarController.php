<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class PopularCarController extends Controller
{
    public function __invoke(): View
    {
        $popularCars = SiteCache::remember('popular-cars:v2', static fn () => Car::query()
            ->with([
                'brand:id,name,slug',
                'configurations:id,car_id,price,currency',
            ])
            ->whereHas('brand')
            ->popular()
            ->limit(40)
            ->get());

        return view('site.popular-cars', [
            'popularCars' => $popularCars,
        ]);
    }
}
