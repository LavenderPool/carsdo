<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Engine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EngineCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_engine_admin(): void
    {
        $response = $this->get(route('admin.engines.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_engines(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Brand $brand */
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => true,
        ]);
        /** @var Brand $anotherBrand */
        $anotherBrand = Brand::query()->create([
            'name' => 'BMW',
            'slug' => 'bmw',
            'leave_from_russian' => false,
        ]);

        $this->actingAs($user)
            ->post(route('admin.engines.store'), [
                'brand_id' => $brand->id,
                'name' => 'Long Range',
                'slug' => '',
            ])
            ->assertRedirect(route('admin.engines.index'));

        $engine = Engine::query()->firstOrFail();

        $this->assertSame($brand->id, $engine->brand_id);
        $this->assertSame('Long Range', $engine->name);
        $this->assertSame('long-range', $engine->slug);

        $this->actingAs($user)
            ->put(route('admin.engines.update', $engine), [
                'brand_id' => $anotherBrand->id,
                'name' => 'Hybrid Max',
                'slug' => 'hybrid-max',
            ])
            ->assertRedirect(route('admin.engines.index'));

        $engine->refresh();

        $this->assertSame($anotherBrand->id, $engine->brand_id);
        $this->assertSame('Hybrid Max', $engine->name);
        $this->assertSame('hybrid-max', $engine->slug);

        $this->actingAs($user)
            ->delete(route('admin.engines.destroy', $engine))
            ->assertRedirect(route('admin.engines.index'));

        $this->assertDatabaseCount('engines', 0);
    }
}
