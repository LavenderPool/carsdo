<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DangerController;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DangerConvertTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_preview_or_apply_convert(): void
    {
        $this->get('/admin/danger/convert')
            ->assertRedirect(route('login'));

        $this->post('/admin/danger/convert')
            ->assertRedirect(route('login'));
    }

    public function test_preview_shows_only_configurations_below_threshold(): void
    {
        $car = $this->createCar('Audi', 'A6');
        $group = $this->createGroup($car);

        $this->createConfiguration($car, $group, 499000);
        $this->createConfiguration($car, $group, 300000);
        $this->createConfiguration($car, $group, 500000);
        $this->createConfiguration($car, $group, 800000);

        $props = $this->buildPreviewData();

        $this->assertSame(500000, $props['threshold']);
        $this->assertSame('$', $props['targetCurrency']);
        $this->assertSame(1, $props['carsCount']);
        $this->assertSame(2, $props['configurationsCount']);
        $this->assertCount(2, $props['cars'][0]['configurations']);
    }

    public function test_preview_ignores_configurations_already_in_target_currency(): void
    {
        $car = $this->createCar('BMW', 'X5');
        $group = $this->createGroup($car);

        $this->createConfiguration($car, $group, 400000, '$');
        $this->createConfiguration($car, $group, 450000);

        $props = $this->buildPreviewData();

        $this->assertSame(1, $props['configurationsCount']);
    }

    public function test_apply_changes_currency_for_configurations_below_threshold(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Tesla', 'Model 3');
        $group = $this->createGroup($car);

        $below = $this->createConfiguration($car, $group, 490000);
        $atThreshold = $this->createConfiguration($car, $group, 500000);
        $above = $this->createConfiguration($car, $group, 600000);

        $response = $this->actingAs($user)->post(route('admin.danger.convert.apply'));

        $response
            ->assertRedirect(route('admin.danger.convert'))
            ->assertSessionHas('success', 'Валюта изменена на $ у 1 конфигураций.');

        $this->assertDatabaseHas('car_configurations', [
            'id' => $below->id,
            'currency' => '$',
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $atThreshold->id,
            'currency' => null,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $above->id,
            'currency' => null,
        ]);
    }

    public function test_apply_is_idempotent_when_nothing_to_change(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Kia', 'EV6');
        $group = $this->createGroup($car);

        $this->createConfiguration($car, $group, 400000, '$');
        $this->createConfiguration($car, $group, 700000);

        $response = $this->actingAs($user)->post(route('admin.danger.convert.apply'));

        $response
            ->assertRedirect(route('admin.danger.convert'))
            ->assertSessionHas('success', 'Конфигурации для смены валюты не найдены.');
    }

    private function createCar(string $brandName, string $carName): Car
    {
        $brand = Brand::query()->create([
            'name' => $brandName,
            'slug' => str($brandName)->slug()->toString(),
            'leave_from_russian' => true,
        ]);

        return Car::query()->create([
            'brand_id' => $brand->id,
            'name' => $carName,
            'slug' => str($carName)->slug()->toString(),
            'year' => '2024',
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
            'start_price' => 5000000,
            'end_price' => 7000000,
            'cover_path' => null,
        ]);
    }

    private function createGroup(Car $car): CarConfigurationGroup
    {
        return CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Base',
            'order' => 1,
            'import_index' => 1,
        ]);
    }

    private function createConfiguration(
        Car $car,
        CarConfigurationGroup $group,
        int $price,
        ?string $currency = null,
    ): CarConfiguration {
        return CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'local_id' => null,
            'have_page' => true,
            'import_index' => null,
            'price' => $price,
            'currency' => $currency,
            'engine_type' => 'petrol',
            'engine_capacity' => 2.0,
            'horsepower' => 249,
            'transmission' => 'automatic',
            'drive_type' => 'awd',
            'fuel_city' => 10.5,
            'fuel_highway' => 7.2,
            'fuel_combined' => 8.6,
            'acceleration' => 6.5,
            'speed' => 240,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPreviewData(): array
    {
        $controller = app(DangerController::class);
        $method = new \ReflectionMethod($controller, 'buildConvertPreview');
        $method->setAccessible(true);

        /** @var array<string, mixed> $preview */
        $preview = $method->invoke($controller);

        return $preview;
    }
}
