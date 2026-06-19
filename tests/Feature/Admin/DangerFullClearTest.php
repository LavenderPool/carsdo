<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\City;
use App\Models\Dealer;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DangerFullClearTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_run_full_clear(): void
    {
        $this->get('/admin/danger/full-clear')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_full_clear_brands_cars_and_related_records(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Brand $brand */
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => true,
        ]);

        /** @var Car $car */
        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Model Y',
            'slug' => 'model-y',
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
            'start_price' => 50000,
            'end_price' => 65000,
            'cover_path' => null,
        ]);
        $city = City::query()->create([
            'name' => 'Москва',
            'slug' => 'moscow',
        ]);
        $dealer = Dealer::query()->create([
            'name' => 'Tesla Store',
        ]);

        $group = CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Long Range',
            'order' => 1,
            'import_index' => 0,
        ]);

        $configuration = CarConfiguration::query()->create([
            'car_id' => $car->id,
            'car_configuration_group_id' => $group->id,
            'import_index' => 0,
            'price' => 54000,
            'engine_type' => 'electric',
            'engine_capacity' => null,
            'horsepower' => 384,
            'transmission' => 'single-speed',
            'drive_type' => 'awd',
            'fuel_city' => 0,
            'fuel_highway' => 0,
            'fuel_combined' => 0,
            'acceleration' => 5.0,
            'speed' => 217,
        ]);

        $category = CarConfigurationEquipmentCategory::query()->create([
            'car_configuration_id' => $configuration->id,
            'name' => 'Комфорт',
            'import_index' => 0,
        ]);

        CarConfigurationEquipment::query()->create([
            'car_configuration_id' => $configuration->id,
            'car_configuration_equipment_category_id' => $category->id,
            'import_index' => 0,
            'value' => 'Подогрев сидений',
            'is_extension' => false,
            'price' => 2000,
        ]);

        CarCrashTest::query()->create([
            'car_id' => $car->id,
            'year' => 2024,
            'rating' => 5,
            'video_path' => '/videos/model-y',
        ]);

        CarReview::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'type' => 'good',
            'value' => 'Быстрый и просторный',
        ]);

        CarTestDrive::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'author' => 'Autoblog',
            'video_path' => '/test-drive/model-y',
        ]);
        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $city->id,
            'dealer_id' => $dealer->id,
            'address' => 'Ленинградский проспект, 10',
            'phone' => '+7 (495) 000-00-00',
            'website' => 'https://example.com/tesla-store',
            'is_official' => true,
        ]);

        $photoGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Экстерьер',
        ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/tesla/model-y/front.jpg',
        ]);

        Storage::disk('public')->put('covers/tesla/model-y/cover.jpg', 'cover');
        Storage::disk('public')->put('images/tesla/model-y/front.jpg', 'front');
        $this->assertTrue(Storage::disk('public')->exists('covers/tesla/model-y/cover.jpg'));
        $this->assertTrue(Storage::disk('public')->exists('images/tesla/model-y/front.jpg'));

        $response = $this->actingAs($user)->get(route('admin.danger.full-clear'));

        $response
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success', 'Все бренды, автомобили и связанные записи удалены.');

        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('cars', 0);
        $this->assertDatabaseCount('cities', 0);
        $this->assertDatabaseCount('dealers', 0);
        $this->assertDatabaseCount('car_dealers', 0);
        $this->assertDatabaseCount('car_crash_tests', 0);
        $this->assertDatabaseCount('car_test_drives', 0);
        $this->assertDatabaseCount('car_reviews', 0);
        $this->assertDatabaseCount('car_configuration_groups', 0);
        $this->assertDatabaseCount('car_configurations', 0);
        $this->assertDatabaseCount('car_configuration_equipment_categories', 0);
        $this->assertDatabaseCount('car_configuration_equipment', 0);
        $this->assertDatabaseCount('car_photo_groups', 0);
        $this->assertDatabaseCount('car_photos', 0);
        $this->assertFalse(Storage::disk('public')->exists('covers/tesla/model-y/cover.jpg'));
        $this->assertFalse(Storage::disk('public')->exists('images/tesla/model-y/front.jpg'));
    }
}
