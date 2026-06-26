<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\Site\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const FILTER_KEYS = [
        'brand',
        'price_min',
        'price_max',
        'engine_capacity_min',
        'engine_capacity_max',
        'engine_types',
        'transmissions',
        'fuel_combined_min',
        'fuel_combined_max',
        'drive_types',
        'horsepower_min',
        'horsepower_max',
        'acceleration_min',
        'acceleration_max',
        'sort',
    ];

    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    public function index(Request $request): View
    {
        $results = $this->searchService->search(
            $request->query('q'),
            $request->only(self::FILTER_KEYS),
            $request->query('sort'),
            $request->integer('page', 1)
        );

        return view('site.search', [
            'query' => $results['query'],
            'queryTooShort' => $results['queryTooShort'],
            'hasSearchableQuery' => $results['hasSearchableQuery'],
            'hasActiveFilters' => $results['hasActiveFilters'],
            'filters' => $results['filters'],
            'filterOptions' => $results['filterOptions'],
            'brandOptions' => $results['brandOptions'],
            'rangeBounds' => $results['rangeBounds'],
            'brands' => $results['brands'],
            'models' => $results['models'],
            'sort' => $results['sort'],
            'sortOptions' => $results['sortOptions'],
            'minSearchQueryLength' => $this->searchService->minQueryLength(),
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $results = $this->searchService->suggest($request->query('q'));

        return response()->json([
            'query' => $results['query'],
            'minQueryLength' => $this->searchService->minQueryLength(),
            'brands' => $results['brands']->map(static fn ($brand): array => [
                'name' => $brand->name,
                'slug' => $brand->slug,
                'url' => '/'.$brand->slug.'/',
            ])->values(),
            'models' => $results['models']->map(static fn ($car): array => [
                'name' => $car->name,
                'brand_name' => $car->brand?->name ?? '',
                'year' => $car->year,
                'url' => '/'.$car->brand->slug.'/'.$car->slug.'/',
            ])->values(),
        ]);
    }
}
