<?php

namespace Tests\Feature\Site;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_filters_models_by_normalized_transmission_value(): void
    {
        $brand = Brand::create([
            'name' => 'Geely',
            'slug' => 'geely',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Atlas Pro',
            'slug' => 'atlas-pro',
        ], [
            'transmission' => 'AT',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Okavango',
            'slug' => 'okavango',
        ], [
            'transmission' => 'АТ',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Emgrand',
            'slug' => 'emgrand',
        ], [
            'transmission' => 'MT',
        ]);

        $response = $this->get(route('search', [
            'transmissions' => ['automatic'],
        ]));

        $response
            ->assertOk()
            ->assertSee('Atlas Pro')
            ->assertSee('Okavango')
            ->assertDontSee('Emgrand');
    }

    public function test_search_page_filters_models_by_imported_configuration_codes(): void
    {
        $brand = Brand::create([
            'name' => 'Zeekr',
            'slug' => 'zeekr',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => '001 AWD',
            'slug' => '001-awd',
        ], [
            'engine_type' => 'electric',
            'transmission' => 'single-speed',
            'drive_type' => 'awd',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'X Front',
            'slug' => 'x-front',
        ], [
            'engine_type' => 'electric',
            'transmission' => 'single-speed',
            'drive_type' => 'fwd',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => '009 Hybrid',
            'slug' => '009-hybrid',
        ], [
            'engine_type' => 'hybrid',
            'transmission' => 'AT',
            'drive_type' => 'awd',
        ]);

        $response = $this->get(route('search', [
            'engine_types' => ['electric'],
            'transmissions' => ['automatic'],
            'drive_types' => ['full'],
        ]));

        $response
            ->assertOk()
            ->assertSee('001 AWD')
            ->assertDontSee('X Front')
            ->assertDontSee('009 Hybrid');
    }

    public function test_search_page_combines_text_query_and_configuration_filters(): void
    {
        $geely = Brand::create([
            'name' => 'Geely',
            'slug' => 'geely',
        ]);

        $toyota = Brand::create([
            'name' => 'Toyota',
            'slug' => 'toyota',
        ]);

        $this->createCarWithConfiguration($geely, [
            'name' => 'Monjaro Hybrid',
            'slug' => 'monjaro-hybrid',
        ], [
            'engine_type' => 'гибрид',
        ]);

        $this->createCarWithConfiguration($geely, [
            'name' => 'Coolray',
            'slug' => 'coolray',
        ], [
            'engine_type' => 'бензин',
        ]);

        $this->createCarWithConfiguration($toyota, [
            'name' => 'RAV4 Hybrid',
            'slug' => 'rav4-hybrid',
        ], [
            'engine_type' => 'гибрид',
        ]);

        $response = $this->get(route('search', [
            'q' => 'Geely',
            'engine_types' => ['hybrid'],
        ]));

        $response
            ->assertOk()
            ->assertSee('Monjaro Hybrid')
            ->assertDontSee('Coolray')
            ->assertDontSee('RAV4 Hybrid');
    }

    public function test_search_page_renders_grouped_price_inputs_without_slider_constraints(): void
    {
        $brand = Brand::create([
            'name' => 'Chery',
            'slug' => 'chery',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Tiggo 7',
            'slug' => 'tiggo-7',
        ], [
            'price' => 2495000,
            'engine_capacity' => 1.55,
            'horsepower' => 147,
            'fuel_combined' => 5.4,
            'acceleration' => 10.49,
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Tiggo 8',
            'slug' => 'tiggo-8',
        ], [
            'price' => 3234000,
            'engine_capacity' => 2.75,
            'horsepower' => 249,
            'fuel_combined' => 7.1,
            'acceleration' => 6.51,
        ]);

        $response = $this->get(route('search'));

        $response
            ->assertOk()
            ->assertSee('name="price_min"', false)
            ->assertSee('name="price_max"', false)
            ->assertSee('type="text"', false)
            ->assertSee('data-grouped-number', false)
            ->assertSee('name="engine_capacity_min"', false)
            ->assertSee('step="0.1"', false)
            ->assertDontSee('type="range"', false);
    }

    public function test_search_page_applies_filters_when_query_is_too_short(): void
    {
        $brand = Brand::create([
            'name' => 'Exeed',
            'slug' => 'exeed',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'RX Fast',
            'slug' => 'rx-fast',
        ], [
            'horsepower' => 249,
            'acceleration' => 6.5,
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'LX Calm',
            'slug' => 'lx-calm',
        ], [
            'horsepower' => 147,
            'acceleration' => 10.5,
        ]);

        $response = $this->get(route('search', [
            'q' => 'x',
            'horsepower_min' => 200,
            'acceleration_max' => 7.0,
        ]));

        $response
            ->assertOk()
            ->assertSee('RX Fast')
            ->assertDontSee('LX Calm')
            ->assertSee('не был учтен');
    }

    public function test_search_page_filters_models_by_displayed_car_price_range(): void
    {
        $brand = Brand::create([
            'name' => 'Lixiang',
            'slug' => 'lixiang',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'L7',
            'slug' => 'l7',
            'start_price' => '2100000',
            'end_price' => '2400000',
        ], [
            'price' => 4500000,
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'L9',
            'slug' => 'l9',
            'start_price' => '3200000',
            'end_price' => '3600000',
        ], [
            'price' => 4700000,
        ]);

        $response = $this->get(route('search', [
            'price_min' => '2000000',
            'price_max' => '2500000',
        ]));

        $response
            ->assertOk()
            ->assertSee('L7')
            ->assertSee('2 100 000 - 2 400 000')
            ->assertDontSee('L9');
    }

    public function test_search_page_filters_models_by_grouped_price_input(): void
    {
        $brand = Brand::create([
            'name' => 'Aito',
            'slug' => 'aito',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'M5',
            'slug' => 'm5',
            'start_price' => '2100000',
            'end_price' => '2200000',
        ], [
            'price' => 4300000,
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'M9',
            'slug' => 'm9',
            'start_price' => '3100000',
            'end_price' => '3300000',
        ], [
            'price' => 5100000,
        ]);

        $response = $this->get(route('search', [
            'price_min' => '2 000 000',
            'price_max' => '2 500 000',
        ]));

        $response
            ->assertOk()
            ->assertSee('M5')
            ->assertDontSee('M9');
    }

    public function test_search_page_sorts_models_by_price_ascending(): void
    {
        $brand = Brand::create([
            'name' => 'Avatr',
            'slug' => 'avatr',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Avatr 12',
            'slug' => 'avatr-12',
            'start_price' => '3900000',
            'end_price' => '4100000',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Avatr 11',
            'slug' => 'avatr-11',
            'start_price' => '2900000',
            'end_price' => '3200000',
        ]);

        $response = $this->get(route('search', [
            'sort' => 'price_asc',
        ]));

        $response
            ->assertOk()
            ->assertSeeInOrder(['Avatr 11', 'Avatr 12'])
            ->assertSee('Сначала дешевле');
    }

    public function test_search_page_sorts_models_by_price_descending_with_end_price_fallback(): void
    {
        $brand = Brand::create([
            'name' => 'Rox',
            'slug' => 'rox',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Rox Alpha',
            'slug' => 'rox-alpha',
            'start_price' => '',
            'end_price' => '3700000',
        ]);

        $this->createCarWithConfiguration($brand, [
            'name' => 'Rox Beta',
            'slug' => 'rox-beta',
            'start_price' => '2800000',
            'end_price' => '3000000',
        ]);

        $response = $this->get(route('search', [
            'sort' => 'price_desc',
        ]));

        $response
            ->assertOk()
            ->assertSeeInOrder(['Rox Alpha', 'Rox Beta']);
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
