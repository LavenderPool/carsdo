<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Engine;
use App\Support\Cache\SiteCache;
use App\Support\RecentViews;
use Illuminate\Contracts\View\View;

class EngineController extends Controller
{
    public function index(): View
    {
        return view('site.engine.index', [
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

    public function brand(Brand $brand): View
    {
        abort_unless($brand->engines()->exists(), 404);

        return view('site.engine.brand', [
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
                ->withCount('configurations')
                ->orderByDesc('views_count')
                ->orderBy('name')
                ->get()),
        ]);
    }

    public function show(Brand $brand, string $engineSlug, RecentViews $recentViews): View
    {
        $engine = Engine::query()
            ->select(['id', 'brand_id', 'slug'])
            ->where('brand_id', $brand->id)
            ->where('slug', $engineSlug)
            ->firstOrFail();

        if ($recentViews->remember("engine:{$engine->id}")) {
            $engine->incrementQuietly('views_count');
        }

        $engine = SiteCache::remember("engine:{$engine->id}:show:v2", fn () => Engine::query()
            ->whereKey($engine->id)
            ->with([
                'brand:id,name,slug',
                'configurations:id,car_id,car_configuration_group_id,local_id,price,currency,engine_id,engine_capacity,horsepower,transmission,drive_type',
                'configurations.group:id,car_id,name,order,import_index',
                'configurations.car:id,brand_id,name,slug,cover_path,start_price,end_price,is_soon,is_another_models,year,is_electric_car',
                'configurations.car.brand:id,name,slug',
            ])
            ->withCount('configurations')
            ->firstOrFail());

        abort_if($engine->brand_id !== $brand->id, 404);

        return view('site.engine.show', [
            'brand' => $brand,
            'engine' => $engine,
        ]);
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
}
