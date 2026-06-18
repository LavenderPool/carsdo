<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeoOverrideTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_setting_seo_overrides(): void
    {
        Setting::query()->create([
            'id' => 1,
            'brand_name' => 'CarsDo',
            'home_seo_title' => 'Главная {site_name}',
            'home_seo_description' => 'Каталог {site_name}',
            'home_seo_h1' => 'Новый каталог {site_name}',
            'home_seo_canonical_url' => '/landing/',
            'home_seo_robots' => 'noindex, nofollow',
        ]);

        config([
            'seo.site_name' => 'CarsDo',
            'seo.admin.default_robots' => 'index, follow',
            'seo.admin.pages.home' => [
                'title' => 'Главная {site_name}',
                'description' => 'Каталог {site_name}',
                'h1' => 'Новый каталог {site_name}',
                'canonical_url' => '/landing/',
                'robots' => 'noindex, nofollow',
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Главная CarsDo', false);
        $response->assertSee('Каталог CarsDo', false);
        $response->assertSee('Новый каталог CarsDo');
        $response->assertSee('/landing/', false);
        $response->assertSee('content="noindex, nofollow"', false);
    }

    public function test_brand_and_car_pages_use_model_seo_overrides(): void
    {
        Setting::query()->create([
            'id' => 1,
            'brand_name' => 'CarsDo',
        ]);
        config([
            'seo.site_name' => 'CarsDo',
            'seo.admin.default_robots' => 'index, follow',
        ]);

        $brand = Brand::query()->create([
            'name' => 'BMW',
            'slug' => 'bmw',
            'leave_from_russian' => false,
            'seo_title' => '{brand} special title',
            'seo_description' => 'Описание для {brand}',
            'seo_h1' => 'Новый H1 {brand}',
            'seo_canonical_url' => '/brands/bmw-special/',
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'X5',
            'slug' => 'x5',
            'year' => '2026',
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
            'seo_title' => '{brand} {car} custom',
            'seo_description' => 'Описание {brand} {car}',
            'seo_h1' => 'H1 {car}',
            'equipment_seo_title' => 'Комплектация {group}',
            'equipment_seo_h1' => '{car} {group}',
            'equipment_seo_canonical_url' => '/special-equipment/',
        ]);

        $group = CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Premium',
            'order' => 1,
            'import_index' => 0,
        ]);

        CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'import_index' => 0,
            'price' => 7900000,
        ]);

        $brandResponse = $this->get('/bmw');

        $brandResponse->assertOk();
        $brandResponse->assertSee('BMW special title', false);
        $brandResponse->assertSee('Описание для BMW', false);
        $brandResponse->assertSee('Новый H1 BMW');
        $brandResponse->assertSee('/brands/bmw-special/', false);

        $carResponse = $this->get('/bmw/x5');

        $carResponse->assertOk();
        $carResponse->assertSee('BMW X5 custom', false);
        $carResponse->assertSee('Описание BMW X5', false);
        $carResponse->assertSee('H1 X5');

        $equipmentResponse = $this->get('/bmw/x5/equipment-1/');

        $equipmentResponse->assertOk();
        $equipmentResponse->assertSee('Комплектация Premium', false);
        $equipmentResponse->assertSee('X5 Premium');
        $equipmentResponse->assertSee('/special-equipment/', false);
    }
}
