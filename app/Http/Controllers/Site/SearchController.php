<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\Site\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    public function index(Request $request): View
    {
        $results = $this->searchService->search(
            $request->query('q'),
            $request->integer('page', 1)
        );

        return view('site.search', [
            'query' => $results['query'],
            'brands' => $results['brands'],
            'models' => $results['models'],
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
