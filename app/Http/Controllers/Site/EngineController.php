<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Engine;
use App\Support\Cache\SiteCache;
use App\Support\RecentViews;
use Illuminate\Http\Response;

class EngineController extends Controller
{
    private const SHOW_VIEWS_COUNT_PLACEHOLDER = '__ENGINE_VIEWS_COUNT__';

    public function index(): Response
    {
        return $this->cachedResponse('engines:index:html:v1', 'site.engine.index', [
            'engineBrands' => $this->engineBrands(),
            'popularEngines' => SiteCache::remember('engines:index:popular:v2', fn () => Engine::query()
                ->select([
                    'id',
                    'brand_id',
                    'name',
                    'slug',
                    'engine_type',
                    'displacement_cc',
                    'max_horsepower',
                    'views_count',
                ])
                ->with(['brand:id,name,slug'])
                ->whereHas('brand')
                ->popular()
                ->orderBy('name')
                ->limit(12)
                ->get()),
        ]);
    }

    public function brand(Brand $brand): Response
    {
        abort_unless($brand->engines()->exists(), 404);

        return $this->cachedResponse("engines:brand:{$brand->id}:html:v1", 'site.engine.brand', [
            'engineBrands' => $this->engineBrands(),
            'selectedEngineBrand' => $brand,
            'engines' => SiteCache::remember("engines:brand:{$brand->id}:v2", fn () => Engine::query()
                ->select([
                    'id',
                    'brand_id',
                    'name',
                    'slug',
                    'engine_type',
                    'displacement_cc',
                    'max_horsepower',
                    'views_count',
                ])
                ->where('brand_id', $brand->id)
                ->orderByDesc('views_count')
                ->orderBy('name')
                ->get()),
        ]);
    }

    public function show(Brand $brand, string $engineSlug, RecentViews $recentViews): Response
    {
        $engine = Engine::query()
            ->select(['id', 'brand_id', 'slug', 'views_count'])
            ->where('brand_id', $brand->id)
            ->where('slug', $engineSlug)
            ->firstOrFail();

        if ($recentViews->remember("engine:{$engine->id}")) {
            $engine->incrementQuietly('views_count');
        }

        $cachedEngine = SiteCache::remember("engine:{$engine->id}:show:v2", fn () => Engine::query()
            ->whereKey($engine->id)
            ->with(['brand:id,name,slug'])
            ->firstOrFail());

        abort_if($cachedEngine->brand_id !== $brand->id, 404);

        return $this->cachedResponse(
            "engine:{$engine->id}:show:html:v1",
            'site.engine.show',
            [
                'brand' => $brand,
                'engine' => $cachedEngine,
                'viewsCountLabel' => self::SHOW_VIEWS_COUNT_PLACEHOLDER,
            ],
            [
                self::SHOW_VIEWS_COUNT_PLACEHOLDER => $this->formatViewsCountLabel($engine->views_count),
            ],
        );
    }

    private function engineBrands()
    {
        return SiteCache::remember('engines:brands:v2', fn () => Brand::query()
            ->select(['id', 'name', 'slug'])
            ->withCount('engines')
            ->whereHas('engines')
            ->orderBy('name')
            ->get());
    }

    private function cachedResponse(string $cacheKey, string $view, array $data, array $replacements = []): Response
    {
        $html = SiteCache::remember($cacheKey, fn () => view($view, $data)->render());

        if ($replacements !== []) {
            $html = strtr($html, $replacements);
        }

        return response($html);
    }

    private function formatViewsCountLabel(int $viewsCount): string
    {
        return number_format($viewsCount, 0, ',', ' ') . ' просмотров';
    }
}
