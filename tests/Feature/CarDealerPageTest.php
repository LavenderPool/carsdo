<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\CarDealer;
use App\Models\City;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarDealerPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_car_page_shows_unique_dealer_cities_and_splits_after_twenty_two_items(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        for ($index = 1; $index <= 23; $index++) {
            $city = City::query()->create([
                'name' => sprintf('City %02d', $index),
                'slug' => sprintf('city-%02d', $index),
            ]);

            $dealer = Dealer::query()->create([
                'name' => sprintf('Dealer %02d', $index),
            ]);

            CarDealer::query()->create([
                'car_id' => $car->id,
                'city_id' => $city->id,
                'dealer_id' => $dealer->id,
                'address' => sprintf('Address %02d', $index),
                'phone' => sprintf('+7 900 000 00 %02d', $index),
                'website' => sprintf('https://example.com/dealers/%02d', $index),
                'is_official' => $index % 2 === 0,
            ]);
        }

        $duplicateCity = City::query()->where('slug', 'city-05')->firstOrFail();
        $extraDealer = Dealer::query()->create([
            'name' => 'Dealer duplicate',
        ]);

        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $duplicateCity->id,
            'dealer_id' => $extraDealer->id,
            'address' => 'Second address',
            'phone' => '+7 900 000 00 99',
            'website' => 'https://example.com/dealers/duplicate',
            'is_official' => false,
        ]);

        $response = $this->get('/tesla/model-y');

        $response
            ->assertOk()
            ->assertSee('Другие города')
            ->assertSee('/tesla/model-y/city-05', false);

        $content = $response->getContent();

        $this->assertSame(1, substr_count($content, 'City 05'));
        $this->assertNotFalse(strpos($content, 'City 22'));
        $this->assertNotFalse(strpos($content, 'Другие города'));
        $this->assertNotFalse(strpos($content, 'City 23'));
        $this->assertTrue(strpos($content, 'City 22') < strpos($content, 'Другие города'));
        $this->assertTrue(strpos($content, 'Другие города') < strpos($content, 'City 23'));
    }

    public function test_equipment_page_reuses_dealer_city_block(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $group = CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Base',
            'order' => 1,
            'import_index' => 0,
        ]);

        CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'local_id' => 101,
            'import_index' => 0,
            'price' => 3500000,
        ]);

        $city = City::query()->create([
            'name' => 'Moscow',
            'slug' => 'moscow',
        ]);
        $dealer = Dealer::query()->create([
            'name' => 'Tesla Center',
        ]);

        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $city->id,
            'dealer_id' => $dealer->id,
            'address' => 'Lenina 1',
            'phone' => '+7 495 000 00 00',
            'website' => 'https://example.com/tesla-center',
            'is_official' => true,
        ]);

        $this->get('/tesla/model-y/equipment-101/')
            ->assertOk()
            ->assertSee('Moscow')
            ->assertSee('/tesla/model-y/moscow', false);
    }

    public function test_city_page_shows_only_selected_city_dealers_and_404_for_missing_city_relation(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $cityA = City::query()->create([
            'name' => 'Moscow',
            'slug' => 'moscow',
        ]);
        $cityB = City::query()->create([
            'name' => 'Kazan',
            'slug' => 'kazan',
        ]);
        $cityC = City::query()->create([
            'name' => 'Perm',
            'slug' => 'perm',
        ]);

        $dealerA = Dealer::query()->create([
            'name' => 'Tesla Moscow',
        ]);
        $dealerB = Dealer::query()->create([
            'name' => 'Tesla Kazan',
        ]);

        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $cityA->id,
            'dealer_id' => $dealerA->id,
            'address' => 'Tverskaya 1',
            'phone' => '+7 495 111 11 11',
            'website' => 'https://example.com/tesla-moscow',
            'is_official' => true,
        ]);
        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $cityB->id,
            'dealer_id' => $dealerB->id,
            'address' => 'Baumana 10',
            'phone' => '+7 843 222 22 22',
            'website' => 'https://example.com/tesla-kazan',
            'is_official' => false,
        ]);

        $this->get('/tesla/model-y/moscow')
            ->assertOk()
            ->assertSee('Tesla Moscow')
            ->assertSee('Tverskaya 1')
            ->assertDontSee('Tesla Kazan')
            ->assertDontSee('Baumana 10')
            ->assertSee('Moscow')
            ->assertSee('Kazan');

        $this->get('/tesla/model-y/perm')
            ->assertNotFound();
    }

    public function test_city_page_uses_prepositional_case_for_city_name(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $city = City::query()->create([
            'name' => 'Новосибирск',
            'slug' => 'novosibirsk',
        ]);
        $dealer = Dealer::query()->create([
            'name' => 'Tesla Novosibirsk',
        ]);

        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $city->id,
            'dealer_id' => $dealer->id,
            'address' => 'Красный проспект, 1',
            'phone' => '+7 383 111 11 11',
            'website' => 'https://example.com/tesla-novosibirsk',
            'is_official' => true,
        ]);

        $this->get('/tesla/model-y/novosibirsk')
            ->assertOk()
            ->assertSee('в Новосибирске')
            ->assertSee('Где купить Model Y в Новосибирске');
    }

    /**
     * @return array{0: Brand, 1: Car}
     */
    private function createBrandAndCar(): array
    {
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Model Y',
            'slug' => 'model-y',
            'year' => '2026',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
            'start_price' => 3500000,
        ]);

        $brand->load([
            'cars' => fn ($query) => $query
                ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
                ->orderBy('name'),
        ]);

        return [$brand, $car];
    }
}
