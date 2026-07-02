<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Engine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_includes_engine_statistics(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Brand $brand */
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => true,
        ]);

        foreach (range(1, 12) as $index) {
            $engine = Engine::query()->create([
                'brand_id' => $brand->id,
                'name' => 'Engine '.$index,
                'slug' => 'engine-'.$index,
            ]);

            $engine->forceFill([
                'views_count' => $index * 10,
            ])->save();
        }

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $page = $response->viewData('page');
        $topEngines = data_get($page, 'props.topEngines');

        $response->assertOk();

        $this->assertSame('Admin/Dashboard', data_get($page, 'component'));
        $this->assertSame(12, data_get($page, 'props.enginesCount'));
        $this->assertIsArray($topEngines);
        $this->assertCount(10, $topEngines);
        $this->assertSame([120, 110, 100, 90, 80, 70, 60, 50, 40, 30], array_column($topEngines, 'views_count'));
        $this->assertSame([
            'Engine 12',
            'Engine 11',
            'Engine 10',
            'Engine 9',
            'Engine 8',
            'Engine 7',
            'Engine 6',
            'Engine 5',
            'Engine 4',
            'Engine 3',
        ], array_column($topEngines, 'name'));
    }
}
