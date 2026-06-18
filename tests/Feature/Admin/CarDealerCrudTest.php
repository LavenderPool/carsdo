<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\City;
use App\Models\Dealer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarDealerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_car_dealer_admin(): void
    {
        $this->get(route('admin.car-dealers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_car_dealers(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        [$car, $dealer, $city] = $this->createDependencies();
        $newCity = City::query()->create([
            'name' => 'Санкт-Петербург',
            'slug' => 'spb',
        ]);

        $this->actingAs($user)
            ->post(route('admin.car-dealers.store'), [
                'car_id' => $car->id,
                'dealer_id' => $dealer->id,
                'city_id' => $city->id,
                'address' => 'Тверская, 1',
                'phone' => '+7 495 000-00-00',
                'website' => 'https://example.com/dealers/tesla',
                'is_official' => true,
            ])
            ->assertRedirect(route('admin.car-dealers.index'));

        $carDealer = CarDealer::query()->firstOrFail();

        $this->assertSame($car->id, $carDealer->car_id);
        $this->assertSame($dealer->id, $carDealer->dealer_id);
        $this->assertSame($city->id, $carDealer->city_id);
        $this->assertSame('Тверская, 1', $carDealer->address);
        $this->assertTrue($carDealer->is_official);

        $this->actingAs($user)
            ->put(route('admin.car-dealers.update', $carDealer), [
                'car_id' => $car->id,
                'dealer_id' => $dealer->id,
                'city_id' => $newCity->id,
                'address' => 'Невский, 10',
                'phone' => '+7 812 000-00-00',
                'website' => 'https://example.com/dealers/tesla-spb',
                'is_official' => false,
            ])
            ->assertRedirect(route('admin.car-dealers.index'));

        $carDealer->refresh();

        $this->assertSame($newCity->id, $carDealer->city_id);
        $this->assertSame('Невский, 10', $carDealer->address);
        $this->assertFalse($carDealer->is_official);

        $this->actingAs($user)
            ->delete(route('admin.car-dealers.destroy', $carDealer))
            ->assertRedirect(route('admin.car-dealers.index'));

        $this->assertDatabaseCount('car_dealers', 0);
    }

    public function test_car_dealer_requires_unique_car_dealer_city_combination(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        [$car, $dealer, $city] = $this->createDependencies();

        CarDealer::query()->create([
            'car_id' => $car->id,
            'dealer_id' => $dealer->id,
            'city_id' => $city->id,
            'address' => 'Тверская, 1',
            'phone' => '+7 495 000-00-00',
            'website' => 'https://example.com/dealers/tesla',
            'is_official' => true,
        ]);

        $this->actingAs($user)
            ->from(route('admin.car-dealers.create'))
            ->post(route('admin.car-dealers.store'), [
                'car_id' => $car->id,
                'dealer_id' => $dealer->id,
                'city_id' => $city->id,
                'address' => 'Тверская, 2',
                'phone' => '+7 495 111-11-11',
                'website' => 'https://example.com/dealers/tesla-duplicate',
                'is_official' => false,
            ])
            ->assertRedirect(route('admin.car-dealers.create'))
            ->assertSessionHasErrors(['dealer_id']);

        $this->assertDatabaseCount('car_dealers', 1);
    }

    /**
     * @return array{0: Car, 1: Dealer, 2: City}
     */
    private function createDependencies(): array
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
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        $dealer = Dealer::query()->create([
            'name' => 'Tesla Store',
        ]);

        $city = City::query()->create([
            'name' => 'Москва',
            'slug' => 'moscow',
        ]);

        return [$car, $dealer, $city];
    }
}
