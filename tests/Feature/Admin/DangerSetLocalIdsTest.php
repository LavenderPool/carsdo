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

class DangerSetLocalIdsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_preview_or_apply_local_ids(): void
    {
        $this->get('/admin/danger/set-local-ids')
            ->assertRedirect(route('login'));

        $this->post('/admin/danger/set-local-ids')
            ->assertRedirect(route('login'));
    }

    public function test_preview_shows_only_affected_cars(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $affectedCar = $this->createCar('Audi', 'A6');
        $cleanCar = $this->createCar('BMW', 'X5');

        $affectedGroup = $this->createGroup($affectedCar, 1);
        $cleanGroup = $this->createGroup($cleanCar, 1);

        $this->createConfiguration($affectedCar, $affectedGroup, null, 6500000);
        $this->createConfiguration($affectedCar, $affectedGroup, null, 6700000);
        $this->createConfiguration($cleanCar, $cleanGroup, 10, 7200000);

        $props = $this->buildPreviewData();

        $this->assertSame(1, $props['carsCount']);
        $this->assertSame(2, $props['configurationsCount']);
        $this->assertCount(1, $props['cars']);
        $this->assertSame('A6', $props['cars'][0]['car_name']);
        $this->assertSame('Audi', $props['cars'][0]['brand_name']);
        $this->assertSame(2, $props['cars'][0]['configurations_count']);
    }

    public function test_apply_sets_sequential_local_ids_from_one_when_car_has_no_existing_values(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Tesla', 'Model Y');
        $group = $this->createGroup($car, 1);

        $first = $this->createConfiguration($car, $group, null, 5100000);
        $second = $this->createConfiguration($car, $group, null, 5200000);
        $third = $this->createConfiguration($car, $group, null, 5300000);

        $response = $this->actingAs($user)->post(route('admin.danger.set-local-ids.apply'));

        $response
            ->assertRedirect(route('admin.danger.set-local-ids'))
            ->assertSessionHas('success', 'local_id заполнены у 3 конфигураций в 1 автомобилях.');

        $this->assertDatabaseHas('car_configurations', [
            'id' => $first->id,
            'local_id' => 1,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $second->id,
            'local_id' => 2,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $third->id,
            'local_id' => 3,
        ]);
    }

    public function test_apply_uses_first_free_local_ids_when_initial_sequence_conflicts(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Mercedes-Benz', 'GLE');
        $group = $this->createGroup($car, 1);

        $existing = $this->createConfiguration($car, $group, 4, 9100000);
        $this->createConfiguration($car, $group, 1, 9200000);
        $firstNull = $this->createConfiguration($car, $group, null, 9300000);
        $secondNull = $this->createConfiguration($car, $group, null, 9400000);

        $response = $this->actingAs($user)->post(route('admin.danger.set-local-ids.apply'));

        $response
            ->assertRedirect(route('admin.danger.set-local-ids'))
            ->assertSessionHas('success', 'local_id заполнены у 2 конфигураций в 1 автомобилях.');

        $this->assertDatabaseHas('car_configurations', [
            'id' => $existing->id,
            'local_id' => 4,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $firstNull->id,
            'local_id' => 2,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $secondNull->id,
            'local_id' => 3,
        ]);
    }

    public function test_preview_and_apply_use_gap_before_appending_new_local_ids(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Audi', 'Q7');
        $group = $this->createGroup($car, 1);

        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14] as $localId) {
            $this->createConfiguration($car, $group, $localId, 8000000 + ($localId * 1000));
        }

        $configurationWithNullLocalId = $this->createConfiguration($car, $group, null, 9000000);

        $props = $this->buildPreviewData();
        $this->assertSame(1, $props['carsCount']);
        $this->assertSame(1, $props['configurationsCount']);
        $this->assertSame(10, $props['cars'][0]['starting_local_id']);
        $this->assertSame(10, $props['cars'][0]['configurations'][0]['new_local_id']);

        $applyResponse = $this->actingAs($user)->post(route('admin.danger.set-local-ids.apply'));

        $applyResponse
            ->assertRedirect(route('admin.danger.set-local-ids'))
            ->assertSessionHas('success', 'local_id заполнены у 1 конфигураций в 1 автомобилях.');

        $this->assertDatabaseHas('car_configurations', [
            'id' => $configurationWithNullLocalId->id,
            'local_id' => 10,
        ]);
    }

    public function test_preview_ignores_configurations_without_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Kia', 'EV6');
        $group = $this->createGroup($car, 1);

        $visibleConfiguration = $this->createConfiguration($car, $group, null, 6100000, true);
        $hiddenConfiguration = $this->createConfiguration($car, $group, null, 6200000, false);

        $props = $this->buildPreviewData();
        $this->assertSame(1, $props['carsCount']);
        $this->assertSame(1, $props['configurationsCount']);
        $this->assertCount(1, $props['cars'][0]['configurations']);
        $this->assertSame($visibleConfiguration->id, $props['cars'][0]['configurations'][0]['id']);
        $this->assertNotSame($hiddenConfiguration->id, $props['cars'][0]['configurations'][0]['id']);
    }

    public function test_apply_does_not_set_local_id_for_configurations_without_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $car = $this->createCar('Volvo', 'EX30');
        $group = $this->createGroup($car, 1);

        $visibleConfiguration = $this->createConfiguration($car, $group, null, 4700000, true);
        $hiddenConfiguration = $this->createConfiguration($car, $group, null, 4800000, false);

        $response = $this->actingAs($user)->post(route('admin.danger.set-local-ids.apply'));

        $response
            ->assertRedirect(route('admin.danger.set-local-ids'))
            ->assertSessionHas('success', 'local_id заполнены у 1 конфигураций в 1 автомобилях.');

        $this->assertDatabaseHas('car_configurations', [
            'id' => $visibleConfiguration->id,
            'local_id' => 1,
        ]);
        $this->assertDatabaseHas('car_configurations', [
            'id' => $hiddenConfiguration->id,
            'local_id' => null,
        ]);
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

    private function createGroup(Car $car, int $importIndex): CarConfigurationGroup
    {
        return CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Base',
            'order' => 1,
            'import_index' => $importIndex,
        ]);
    }

    private function createConfiguration(
        Car $car,
        CarConfigurationGroup $group,
        ?int $localId,
        int $price,
        bool $havePage = true,
    ): CarConfiguration {
        return CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'local_id' => $localId,
            'have_page' => $havePage,
            'import_index' => null,
            'price' => $price,
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
        $method = new \ReflectionMethod($controller, 'buildSetLocalIdsPreview');
        $method->setAccessible(true);

        /** @var array<string, mixed> $preview */
        $preview = $method->invoke($controller);

        return $preview;
    }
}
