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
                'engine_url' => 'https://example.com/engines/long-range',
                'engine_type' => 'electric',
                'displacement_cc' => '1498',
                'max_horsepower' => '320',
                'max_power_output_at_rpm' => '320 (235) / 5500',
                'max_torque_at_rpm' => '450 / 3000',
                'valves_per_cylinder' => '4',
                'compression_ratio' => '10.5',
                'cylinder_bore_mm' => '82.5',
                'piston_stroke_mm' => '92.8',
                'valvetrain' => 'DOHC',
                'recommended_fuel_type' => 'electric',
                'fuel_consumption_l_per_100_km' => '18.4',
                'co2_emissions_g_per_km' => '0',
                'has_start_stop_system' => '1',
                'engine_notes' => 'Initial notes',
                'page_text' => 'Initial page text',
            ])
            ->assertRedirect(route('admin.engines.index'));

        $engine = Engine::query()->firstOrFail();

        $this->assertSame($brand->id, $engine->brand_id);
        $this->assertSame('Long Range', $engine->name);
        $this->assertSame('long-range', $engine->slug);
        $this->assertSame('https://example.com/engines/long-range', $engine->engine_url);
        $this->assertSame('electric', $engine->engine_type);
        $this->assertSame('1498', $engine->displacement_cc);
        $this->assertSame('320', $engine->max_horsepower);
        $this->assertSame('320 (235) / 5500', $engine->max_power_output_at_rpm);
        $this->assertSame('450 / 3000', $engine->max_torque_at_rpm);
        $this->assertSame('4', $engine->valves_per_cylinder);
        $this->assertSame('10.5', $engine->compression_ratio);
        $this->assertSame('82.5', $engine->cylinder_bore_mm);
        $this->assertSame('92.8', $engine->piston_stroke_mm);
        $this->assertSame('DOHC', $engine->valvetrain);
        $this->assertSame('electric', $engine->recommended_fuel_type);
        $this->assertSame('18.4', $engine->fuel_consumption_l_per_100_km);
        $this->assertSame('0', $engine->co2_emissions_g_per_km);
        $this->assertTrue($engine->has_start_stop_system);
        $this->assertSame('Initial notes', $engine->engine_notes);
        $this->assertSame('Initial page text', $engine->page_text);

        $this->actingAs($user)
            ->put(route('admin.engines.update', $engine), [
                'brand_id' => $anotherBrand->id,
                'name' => 'Hybrid Max',
                'slug' => 'hybrid-max',
                'engine_url' => '',
                'engine_type' => 'hybrid',
                'displacement_cc' => '1998',
                'max_horsepower' => '245',
                'max_power_output_at_rpm' => '',
                'max_torque_at_rpm' => '400 / 2500',
                'valves_per_cylinder' => '4',
                'compression_ratio' => '',
                'cylinder_bore_mm' => '81',
                'piston_stroke_mm' => '',
                'valvetrain' => 'SOHC',
                'recommended_fuel_type' => 'petrol',
                'fuel_consumption_l_per_100_km' => '',
                'co2_emissions_g_per_km' => '120',
                'has_start_stop_system' => '',
                'engine_notes' => '',
                'page_text' => '',
            ])
            ->assertRedirect(route('admin.engines.index'));

        $engine->refresh();

        $this->assertSame($anotherBrand->id, $engine->brand_id);
        $this->assertSame('Hybrid Max', $engine->name);
        $this->assertSame('hybrid-max', $engine->slug);
        $this->assertNull($engine->engine_url);
        $this->assertSame('hybrid', $engine->engine_type);
        $this->assertSame('1998', $engine->displacement_cc);
        $this->assertSame('245', $engine->max_horsepower);
        $this->assertNull($engine->max_power_output_at_rpm);
        $this->assertSame('400 / 2500', $engine->max_torque_at_rpm);
        $this->assertSame('4', $engine->valves_per_cylinder);
        $this->assertNull($engine->compression_ratio);
        $this->assertSame('81', $engine->cylinder_bore_mm);
        $this->assertNull($engine->piston_stroke_mm);
        $this->assertSame('SOHC', $engine->valvetrain);
        $this->assertSame('petrol', $engine->recommended_fuel_type);
        $this->assertNull($engine->fuel_consumption_l_per_100_km);
        $this->assertSame('120', $engine->co2_emissions_g_per_km);
        $this->assertNull($engine->has_start_stop_system);
        $this->assertNull($engine->engine_notes);
        $this->assertNull($engine->page_text);

        $this->actingAs($user)
            ->delete(route('admin.engines.destroy', $engine))
            ->assertRedirect(route('admin.engines.index'));

        $this->assertDatabaseCount('engines', 0);
    }
}
