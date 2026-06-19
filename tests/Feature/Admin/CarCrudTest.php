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
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use App\Models\City;
use App\Models\Dealer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
                'seo_title' => '{brand} {car} title',
                'seo_description' => 'Car description',
                'seo_h1' => 'Car H1',
                'seo_og_image' => '/images/model-y.jpg',
                'seo_canonical_url' => '/cars/model-y/',
                'seo_robots' => 'index, follow',
                'equipment_seo_title' => '{group} equipment',
                'equipment_seo_h1' => '{car} equipment',
                'dealer_seo_title' => 'Dealer {city}',
                'reviews_seo_title' => 'Reviews {car}',
                'crash_test_seo_title' => 'Crash {car}',
                'test_drive_seo_title' => 'Drive {car}',
                'photo_seo_title' => 'Photo {car}',
            ])
            ->assertRedirect(route('admin.cars.index'));

        $car = Car::query()->firstOrFail();

        $this->assertSame('Model Y', $car->name);
        $this->assertSame('model-y', $car->slug);
        $this->assertTrue($car->is_electric_car);
        $this->assertSame('{brand} {car} title', $car->seo_title);

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
                'seo_title' => 'Model 3 title',
                'seo_description' => 'Updated car description',
                'seo_h1' => 'Model 3 H1',
                'seo_og_image' => '/images/model-3.jpg',
                'seo_canonical_url' => '/cars/model-3/',
                'seo_robots' => 'noindex, nofollow',
                'equipment_seo_title' => 'Equipment title',
                'equipment_seo_h1' => 'Equipment H1',
                'dealer_seo_title' => 'Dealer title',
                'reviews_seo_title' => 'Reviews title',
                'crash_test_seo_title' => 'Crash title',
                'test_drive_seo_title' => 'Drive title',
                'photo_seo_title' => 'Photo title',
            ])
            ->assertRedirect(route('admin.cars.index'));

        $car->refresh();
        $this->assertSame('Model 3', $car->name);
        $this->assertTrue($car->is_soon);
        $this->assertTrue($car->is_another_models);
        $this->assertSame('Model 3 title', $car->seo_title);
        $this->assertSame('noindex, nofollow', $car->seo_robots);
        $this->assertSame('Equipment title', $car->equipment_seo_title);
        $this->assertSame('Dealer title', $car->dealer_seo_title);
        $this->assertSame('Photo title', $car->photo_seo_title);

        $this->actingAs($user)
            ->delete(route('admin.cars.destroy', $car))
            ->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseCount('cars', 0);
    }

    public function test_authenticated_user_can_manage_nested_car_entities(): void
    {
        Storage::fake('public');

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
                'photo' => $this->fakeImageUpload('front.png'),
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

    public function test_authenticated_user_can_upload_replace_and_delete_car_photo_files(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::query()->create([
            'name' => 'Audi',
            'slug' => 'audi',
            'leave_from_russian' => false,
        ]);
        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Q8',
            'slug' => 'q8',
            'year' => '2024',
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
        ]);
        $group = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Экстерьер',
        ]);

        $this->actingAs($user)
            ->post(route('admin.cars.photos.store', $car), [
                'car_photo_group_id' => $group->id,
                'photo' => $this->fakeImageUpload('front.png'),
            ])
            ->assertRedirect(route('admin.cars.photos.index', $car));

        $photo = CarPhoto::query()->firstOrFail();
        $originalPath = $photo->photo_path;

        $this->assertTrue(Storage::disk('public')->exists($originalPath));

        $this->actingAs($user)
            ->put(route('admin.cars.photos.update', [$car, $photo]), [
                'car_photo_group_id' => $group->id,
                'photo' => $this->fakeImageUpload('rear.png'),
            ])
            ->assertRedirect(route('admin.cars.photos.index', $car));

        $photo->refresh();

        $this->assertNotSame($originalPath, $photo->photo_path);
        $this->assertFalse(Storage::disk('public')->exists($originalPath));
        $this->assertTrue(Storage::disk('public')->exists($photo->photo_path));

        $newPath = $photo->photo_path;

        $this->actingAs($user)
            ->delete(route('admin.cars.photos.destroy', [$car, $photo]))
            ->assertRedirect(route('admin.cars.photos.index', $car));

        $this->assertFalse(Storage::disk('public')->exists($newPath));
        $this->assertDatabaseCount('car_photos', 0);
    }

    public function test_car_destroy_cascades_to_all_related_records(): void
    {
        Storage::fake('public');

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
        $city = City::query()->create(['name' => 'Москва', 'slug' => 'moscow']);
        $dealer = Dealer::query()->create(['name' => 'BMW Store']);

        CarCrashTest::query()->create(['car_id' => $car->id, 'year' => 2024, 'rating' => 5]);
        CarTestDrive::query()->create(['car_id' => $car->id, 'import_index' => 0, 'author' => 'Auto', 'video_path' => '/v/1']);
        CarReview::query()->create(['car_id' => $car->id, 'import_index' => 0, 'type' => 'good', 'value' => 'ok']);
        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $city->id,
            'dealer_id' => $dealer->id,
            'address' => 'Тверская, 1',
            'phone' => '+7 495 000-00-00',
            'website' => 'https://example.com/bmw-store',
            'is_official' => true,
        ]);
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
        CarPhoto::query()->create(['car_id' => $car->id, 'car_photo_group_id' => $photoGroup->id, 'photo_path' => 'images/bmw/i5/front.jpg']);

        Storage::disk('public')->put('images/bmw/i5/front.jpg', 'front');
        Storage::disk('public')->put('covers/bmw/i5/cover.jpg', 'cover');

        $this->actingAs($user)
            ->delete(route('admin.cars.destroy', $car))
            ->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseCount('cars', 0);
        $this->assertDatabaseCount('car_crash_tests', 0);
        $this->assertDatabaseCount('car_test_drives', 0);
        $this->assertDatabaseCount('car_reviews', 0);
        $this->assertDatabaseCount('car_dealers', 0);
        $this->assertDatabaseCount('car_configuration_groups', 0);
        $this->assertDatabaseCount('car_configurations', 0);
        $this->assertDatabaseCount('car_configuration_equipment_categories', 0);
        $this->assertDatabaseCount('car_configuration_equipment', 0);
        $this->assertDatabaseCount('car_photo_groups', 0);
        $this->assertDatabaseCount('car_photos', 0);
        $this->assertFalse(Storage::disk('public')->exists('images/bmw/i5/front.jpg'));
        $this->assertFalse(Storage::disk('public')->exists('covers/bmw/i5/cover.jpg'));
    }

    private function fakeImageUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wn8yKkAAAAASUVORK5CYII=',
                true,
            ) ?: '',
        );
    }
}
