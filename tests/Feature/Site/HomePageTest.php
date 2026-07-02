<?php

namespace Tests\Feature\Site;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_footer_brand_lists_only_for_brands_with_cars(): void
    {
        $activeBrand = Brand::create([
            'name' => 'Footer Active Brand',
            'slug' => 'footer-active-brand',
            'leave_from_russian' => false,
        ]);

        $leftBrand = Brand::create([
            'name' => 'Footer Left Brand',
            'slug' => 'footer-left-brand',
            'leave_from_russian' => true,
        ]);

        Brand::create([
            'name' => 'Footer Empty Brand',
            'slug' => 'footer-empty-brand',
            'leave_from_russian' => false,
        ]);

        $this->createCarWithConfiguration($activeBrand, [
            'name' => 'Active Model',
            'slug' => 'active-model',
        ]);
        $this->createCarWithConfiguration($leftBrand, [
            'name' => 'Left Model',
            'slug' => 'left-model',
        ]);

        (new AppServiceProvider($this->app))->boot();
        (new AppServiceProvider($this->app))->boot();

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('/footer-active-brand/', false)
            ->assertSee('Footer Active Brand')
            ->assertSee('/footer-left-brand/', false)
            ->assertSee('Footer Left Brand')
            ->assertDontSee('/footer-empty-brand/', false)
            ->assertDontSee('Footer Empty Brand');
    }

    public function test_home_page_renders_popular_models_filter_accordion_that_submits_to_search(): void
    {
        $brand = Brand::create([
            'name' => 'Geely',
            'slug' => 'geely',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Monjaro',
            'slug' => 'monjaro',
            'views_count' => 150,
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Популярные модели')
            ->assertSee('Показать фильтры')
            ->assertSee('data-filter-accordion-toggle', false)
            ->assertSee('id="homePopularFiltersForm"', false)
            ->assertSee('action="'.route('search').'"', false)
            ->assertSee('aria-controls="homePopularFiltersPanel"', false)
            ->assertSee('Monjaro');
    }

    private function createCarWithConfiguration(Brand $brand, array $carAttributes = [], array $configurationAttributes = []): Car
    {
        $car = Car::create(array_merge([
            'brand_id' => $brand->id,
            'name' => 'Model',
            'slug' => 'model',
            'year' => '2026',
            'start_price' => '2500000',
            'end_price' => '3200000',
        ], $carAttributes));

        $group = CarConfigurationGroup::create([
            'car_id' => $car->id,
            'name' => 'Base',
            'order' => 1,
        ]);

        CarConfiguration::create(array_merge([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'price' => 2800000,
            'engine_type' => 'бензин',
            'engine_capacity' => 2.0,
            'horsepower' => 200,
            'transmission' => 'AT',
            'drive_type' => '4x4 Полный',
            'fuel_combined' => 8.0,
            'acceleration' => 8.0,
        ], $configurationAttributes));

        return $car;
    }
}
