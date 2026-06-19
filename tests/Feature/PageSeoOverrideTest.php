<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\CarPageSeo;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\City;
use App\Models\Dealer;
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
            'local_id' => 101,
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

        $equipmentResponse = $this->get('/bmw/x5/equipment-101/');

        $equipmentResponse->assertOk();
        $equipmentResponse->assertSee('Комплектация Premium', false);
        $equipmentResponse->assertSee('X5 Premium');
        $equipmentResponse->assertSee('/special-equipment/', false);
    }

    public function test_car_page_seo_uses_car_override_before_page_record_for_dealer_page(): void
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
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'X5',
            'slug' => 'x5',
            'year' => '2026',
            'start_price' => 7900000,
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
            'dealer_seo_title' => 'Персональный title {brand} {car}',
        ]);

        $city = City::query()->create([
            'name' => 'Москва',
            'slug' => 'moscow',
        ]);
        $dealer = Dealer::query()->create([
            'name' => 'BMW Центр',
        ]);
        CarDealer::query()->create([
            'car_id' => $car->id,
            'city_id' => $city->id,
            'dealer_id' => $dealer->id,
            'address' => 'ул. Тестовая, 1',
            'phone' => '+7 999 000 00 00',
            'website' => 'https://example.com/dealer',
            'is_official' => true,
        ]);

        CarPageSeo::query()
            ->where('page_key', 'car_dealer')
            ->update([
                'title' => 'Шаблон дилеров {brand} {car}',
                'description' => 'Описание дилеров {city}',
                'h1' => 'Купить {brand} {car} в {city}',
            ]);

        $response = $this->get('/bmw/x5/moscow');

        $response->assertOk();
        $response->assertSee('Персональный title BMW X5', false);
        $response->assertSee('Описание дилеров Москва', false);
        $response->assertSee('Купить BMW X5 в Москва');
    }

    public function test_car_page_seo_uses_page_record_when_car_override_missing_for_photo_page(): void
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
            'name' => 'Audi',
            'slug' => 'audi',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'A6',
            'slug' => 'a6',
            'year' => '2026',
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
        ]);
        $photoGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Галерея',
        ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => '/images/a6-photo.jpg',
        ]);

        CarPageSeo::query()
            ->where('page_key', 'car_photo')
            ->update([
                'title' => 'Фото {brand} {car}',
                'description' => 'Фотогалерея {brand} {car}',
                'h1' => 'Фото {brand} {car} в галерее',
            ]);

        $response = $this->get('/audi/a6/photo/');

        $response->assertOk();
        $response->assertSee('Фото Audi A6', false);
        $response->assertSee('Фотогалерея Audi A6', false);
        $response->assertSee('Фото Audi A6 в галерее');
    }

    public function test_car_page_seo_falls_back_to_factory_defaults_when_overrides_are_empty(): void
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
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Model S',
            'slug' => 'model-s',
            'year' => '2026',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);
        $photoGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Галерея',
        ]);

        CarPageSeo::query()
            ->where('page_key', 'car_photo')
            ->update([
                'title' => null,
                'description' => null,
                'h1' => null,
            ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => '/images/model-s.jpg',
        ]);

        $response = $this->get('/tesla/model-s/photo/');

        $response->assertOk();
        $response->assertSee('Model S - фото салона, новый кузов', false);
        $response->assertSee('Model S - фото нового кузова, фото внутри салона автомобиля', false);
        $response->assertSee('Model S › Фото');
    }
}
