<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_car_admin(): void
    {
        $this->get(route('admin.cars.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_cars(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        $this->actingAs($user)
            ->post(route('admin.cars.store'), [
                'brand_id' => $brand->id,
                'name' => 'Model Y',
                'slug' => '',
                'year' => '2024',
                'cover_path' => '/covers/model-y.jpg',
                'start_price' => 50000,
                'end_price' => 65000,
                'official_site' => 'https://example.com/model-y',
                'is_electric_car' => true,
                'is_soon' => false,
                'is_another_models' => false,
            ])
            ->assertRedirect(route('admin.cars.index'));

        $car = Car::query()->firstOrFail();

        $this->assertSame('Model Y', $car->name);
        $this->assertSame('model-y', $car->slug);
        $this->assertTrue($car->is_electric_car);

        $this->actingAs($user)
            ->put(route('admin.cars.update', $car), [
                'brand_id' => $brand->id,
                'name' => 'Model 3',
                'slug' => 'model-3',
                'year' => '2025',
                'cover_path' => '/covers/model-3.jpg',
                'start_price' => 45000,
                'end_price' => 56000,
                'official_site' => 'https://example.com/model-3',
                'is_electric_car' => true,
                'is_soon' => true,
                'is_another_models' => true,
            ])
            ->assertRedirect(route('admin.cars.index'));

        $car->refresh();
        $this->assertSame('Model 3', $car->name);
        $this->assertTrue($car->is_soon);
        $this->assertTrue($car->is_another_models);

        $this->actingAs($user)
            ->delete(route('admin.cars.destroy', $car))
            ->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseCount('cars', 0);
    }

    public function test_authenticated_user_can_manage_nested_car_entities(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::query()->create([
            'name' => 'Audi',
            'slug' => 'audi',
            'leave_from_russian' => false,
        ]);
        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Q6',
            'slug' => 'q6',
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        $this->actingAs($user)
            ->post(route('admin.cars.crash-tests.store', $car), [
                'year' => 2024,
                'rating' => 5,
                'video_path' => '/videos/q6-crash',
            ])
            ->assertRedirect(route('admin.cars.crash-tests.index', $car));

        $this->actingAs($user)
            ->post(route('admin.cars.test-drives.store', $car), [
                'import_index' => 0,
                'author' => 'Autoblog',
                'video_path' => '/videos/q6-test-drive',
            ])
            ->assertRedirect(route('admin.cars.test-drives.index', $car));

        $this->actingAs($user)
            ->post(route('admin.cars.reviews.store', $car), [
                'import_index' => 0,
                'type' => 'good',
                'value' => 'Отличная управляемость',
            ])
            ->assertRedirect(route('admin.cars.reviews.index', $car));

        $this->actingAs($user)
            ->post(route('admin.cars.configuration-groups.store', $car), [
                'name' => 'Premium',
                'order' => 1,
                'import_index' => 0,
            ])
            ->assertRedirect(route('admin.cars.configuration-groups.index', $car));

        $group = CarConfigurationGroup::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.cars.configurations.store', $car), [
                'car_configuration_group_id' => $group->id,
                'import_index' => 0,
                'price' => 55000,
                'engine_type' => 'electric',
                'horsepower' => 350,
                'transmission' => 'automatic',
                'drive_type' => 'awd',
            ])
            ->assertRedirect(route('admin.cars.configurations.index', $car));

        $configuration = CarConfiguration::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.cars.equipment-categories.store', $car), [
                'car_configuration_group_id' => $group->id,
                'car_configuration_id' => $configuration->id,
                'name' => 'Комфорт',
                'import_index' => 0,
            ])
            ->assertRedirect(route('admin.cars.equipment-categories.index', $car));

        $category = CarConfigurationEquipmentCategory::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.cars.equipment.store', $car), [
                'car_configuration_equipment_category_id' => $category->id,
                'car_configuration_id' => $configuration->id,
                'import_index' => 0,
                'value' => 'Подогрев сидений',
                'is_extension' => false,
                'price' => 2000,
            ])
            ->assertRedirect(route('admin.cars.equipment.index', $car));

        $this->actingAs($user)
            ->post(route('admin.cars.photo-groups.store', $car), [
                'name' => 'Экстерьер',
            ])
            ->assertRedirect(route('admin.cars.photo-groups.index', $car));

        $photoGroup = CarPhotoGroup::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.cars.photos.store', $car), [
                'car_photo_group_id' => $photoGroup->id,
                'photo_path' => '/img/cars/q6/front.jpg',
            ])
            ->assertRedirect(route('admin.cars.photos.index', $car));

        $this->assertDatabaseCount('car_crash_tests', 1);
        $this->assertDatabaseCount('car_test_drives', 1);
        $this->assertDatabaseCount('car_reviews', 1);
        $this->assertDatabaseCount('car_configuration_groups', 1);
        $this->assertDatabaseCount('car_configurations', 1);
        $this->assertDatabaseCount('car_configuration_equipment_categories', 1);
        $this->assertDatabaseCount('car_configuration_equipment', 1);
        $this->assertDatabaseCount('car_photo_groups', 1);
        $this->assertDatabaseCount('car_photos', 1);
    }

    public function test_car_destroy_cascades_to_all_related_records(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::query()->create([
            'name' => 'BMW',
            'slug' => 'bmw',
            'leave_from_russian' => false,
        ]);
        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'i5',
            'slug' => 'i5',
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        CarCrashTest::query()->create(['car_id' => $car->id, 'year' => 2024, 'rating' => 5]);
        CarTestDrive::query()->create(['car_id' => $car->id, 'import_index' => 0, 'author' => 'Auto', 'video_path' => '/v/1']);
        CarReview::query()->create(['car_id' => $car->id, 'import_index' => 0, 'type' => 'good', 'value' => 'ok']);
        $group = CarConfigurationGroup::query()->create(['car_id' => $car->id, 'name' => 'Base', 'order' => 1, 'import_index' => 0]);
        $configuration = CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'import_index' => 0,
            'price' => 55000,
        ]);
        $category = CarConfigurationEquipmentCategory::query()->create([
            'car_configuration_group_id' => $group->id,
            'car_configuration_id' => $configuration->id,
            'name' => 'Comfort',
            'import_index' => 0,
        ]);
        CarConfigurationEquipment::query()->create([
            'car_configuration_equipment_category_id' => $category->id,
            'car_configuration_id' => $configuration->id,
            'import_index' => 0,
            'value' => 'Seat heat',
            'is_extension' => false,
        ]);
        $photoGroup = CarPhotoGroup::query()->create(['car_id' => $car->id, 'name' => 'Main']);
        CarPhoto::query()->create(['car_id' => $car->id, 'car_photo_group_id' => $photoGroup->id, 'photo_path' => '/img/1.jpg']);

        $this->actingAs($user)
            ->delete(route('admin.cars.destroy', $car))
            ->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseCount('cars', 0);
        $this->assertDatabaseCount('car_crash_tests', 0);
        $this->assertDatabaseCount('car_test_drives', 0);
        $this->assertDatabaseCount('car_reviews', 0);
        $this->assertDatabaseCount('car_configuration_groups', 0);
        $this->assertDatabaseCount('car_configurations', 0);
        $this->assertDatabaseCount('car_configuration_equipment_categories', 0);
        $this->assertDatabaseCount('car_configuration_equipment', 0);
        $this->assertDatabaseCount('car_photo_groups', 0);
        $this->assertDatabaseCount('car_photos', 0);
    }
}
