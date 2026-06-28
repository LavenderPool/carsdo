<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\CarOwnerReview;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarOwnerReviewPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviews_page_renders_owner_reviews_without_pros_and_cons(): void
    {
        [$brand, $car] = $this->createBrandAndCar();
        $this->createSupportingContent($car);

        CarOwnerReview::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'rating' => 4,
            'full_name' => 'Иван Петров',
            'photo_path' => '/photos/model-y-owner.jpg',
            'text' => 'Хорошая динамика и удобный салон.',
        ]);

        $this->get('/tesla/model-y/reviews/')
            ->assertOk()
            ->assertSee('Отзывы владельцев')
            ->assertSee('Иван Петров')
            ->assertSee('Хорошая динамика и удобный салон.')
            ->assertSee('/photos/model-y-owner.jpg', false)
            ->assertSee('Рейтинг 4 из 5');
    }

    public function test_car_page_shows_reviews_link_when_only_owner_reviews_exist(): void
    {
        [$brand, $car] = $this->createBrandAndCar();
        $this->createSupportingContent($car);

        CarOwnerReview::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'rating' => 5,
            'full_name' => 'Петр Иванов',
            'text' => 'Машина полностью устраивает.',
        ]);

        $this->get('/tesla/model-y')
            ->assertOk()
            ->assertSee('/tesla/model-y/reviews/', false)
            ->assertSee('ОТЗЫВЫ ВЛАДЕЛЬЦЕВ (1)');
    }

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

        return [$brand, $car];
    }

    private function createSupportingContent(Car $car): void
    {
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

        $photoGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Preview',
        ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => '/photos/model-y-preview.jpg',
        ]);
    }
}
