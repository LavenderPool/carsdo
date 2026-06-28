<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Engine;
use App\Support\Cache\SiteCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

class EnginePageCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_site_cache_restores_engine_collections_from_database_cache(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        Engine::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Alpha Drive',
            'slug' => 'alpha-drive',
            'engine_type' => 'electric',
            'displacement_cc' => '1498',
            'max_horsepower' => '320',
        ]);

        $cachedEngines = SiteCache::remember('tests:engine-collection-cache', static fn () => Engine::query()
            ->with('brand:id,name,slug')
            ->orderBy('id')
            ->get());

        $restoredEngines = SiteCache::remember('tests:engine-collection-cache', static function () {
            throw new RuntimeException('SiteCache should return the stored engine collection.');
        });

        $this->assertCount(1, $cachedEngines);
        $this->assertCount(1, $restoredEngines);
        $this->assertInstanceOf(Engine::class, $restoredEngines->first());
        $this->assertInstanceOf(Brand::class, $restoredEngines->first()->brand);
        $this->assertSame('Alpha Drive', $restoredEngines->first()->name);
        $this->assertSame('Tesla', $restoredEngines->first()->brand->name);
    }

    public function test_engine_page_renders_on_subsequent_requests_after_cache_is_warmed(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        $engine = Engine::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Alpha Drive',
            'slug' => 'alpha-drive',
            'engine_type' => 'electric',
            'displacement_cc' => '1498',
            'max_horsepower' => '320',
            'engine_notes' => 'Original cached note.',
        ]);

        $url = route('engine.show', [
            'brand' => $brand,
            'engine_slug' => $engine->slug,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Alpha Drive')
            ->assertSee('Original cached note.');

        $engine->update([
            'name' => 'Beta Drive',
            'engine_notes' => 'Updated database note.',
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Beta Drive')
            ->assertSee('Updated database note.');
    }
}
