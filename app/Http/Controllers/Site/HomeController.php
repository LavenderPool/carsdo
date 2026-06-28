<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarCrashTest;
use App\Models\CarTestDrive;
use App\Services\Site\SearchService;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    public function __invoke(): View
    {
        $filterMeta = $this->searchService->filterMeta();

        $data = SiteCache::remember('home:v4', function () use ($filterMeta): array {
            $latestTestDriveIds = CarTestDrive::query()
                ->selectRaw('MAX(id)', [])
                ->groupBy('car_id');

            return [
                'newCars' => Car::query()
                    ->with([
                        'brand:id,name,slug',
                        'configurations:id,car_id,price,currency',
                    ])
                    ->whereHas('brand')
                    ->latest()
                    ->limit(12)
                    ->get(),
                'soonCars' => Car::query()
                    ->with([
                        'brand:id,name,slug',
                        'configurations:id,car_id,price,currency',
                    ])
                    ->whereHas('brand')
                    ->where('is_soon', true)
                    ->latest()
                    ->limit(12)
                    ->get(),
                'crashTests' => CarCrashTest::query()
                    ->with(['car.brand:id,name,slug'])
                    ->whereHas('car.brand')
                    ->latest()
                    ->limit(12)
                    ->get(),
                'testDrives' => CarTestDrive::query()
                    ->with(['car.brand:id,name,slug'])
                    ->whereIn('id', $latestTestDriveIds)
                    ->whereHas('car.brand')
                    ->latest()
                    ->limit(12)
                    ->get(),
                'popularCars' => Car::query()
                    ->with([
                        'brand:id,name,slug',
                        'configurations:id,car_id,price,currency',
                    ])
                    ->whereHas('brand')
                    ->popular()
                    ->limit(11)
                    ->get(),
                'searchFilters' => [
                    'query' => '',
                    'filters' => [],
                    'filterOptions' => $filterMeta['filterOptions'],
                    'brandOptions' => $filterMeta['brandOptions'],
                    'rangeBounds' => $filterMeta['rangeBounds'],
                ],
            ];
        });

        return view('site.main', $data);
    }
}
