<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarPhotoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_page_returns_ok_and_renders_grouped_gallery(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $exterior = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Exterior',
        ]);
        $interior = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Interior',
        ]);
        $details = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Details',
        ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $exterior->id,
            'photo_path' => '/photos/model-y-exterior-1.jpg',
        ]);
        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $interior->id,
            'photo_path' => '/photos/model-y-interior-1.jpg',
        ]);
        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $details->id,
            'photo_path' => '/photos/model-y-extra-1.jpg',
        ]);

        $response = $this->get('/tesla/model-y/photo/');
        $canonicalUrl = rtrim((string) config('app.url'), '/').'/tesla/model-y/photo/';

        $response
            ->assertOk()
            ->assertSee('<title>Model Y - фото салона, новый кузов</title>', false)
            ->assertSee('content="Model Y - фото нового кузова, фото внутри салона автомобиля (экстерьер и интерьер) новой модели."', false)
            ->assertSee('rel="canonical" href="'.$canonicalUrl.'"', false)
            ->assertSee('EXTERIOR')
            ->assertSee('INTERIOR')
            ->assertSee('DETAILS')
            ->assertSee('/photos/model-y-exterior-1.jpg', false)
            ->assertSee('/photos/model-y-interior-1.jpg', false)
            ->assertSee('/photos/model-y-extra-1.jpg', false);
    }

    public function test_gallery_preview_block_is_rendered_on_public_pages(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $previewGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Preview',
        ]);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $previewGroup->id,
            'photo_path' => '/photos/model-y-preview-1.jpg',
        ]);
        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $previewGroup->id,
            'photo_path' => '/photos/model-y-preview-2.jpg',
        ]);

        CarCrashTest::query()->create([
            'car_id' => $car->id,
            'year' => 2026,
            'rating' => 5,
            'video_path' => 'https://www.youtube.com/watch?v=fsCIw1y5quU',
        ]);

        CarTestDrive::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'author' => 'AutoBlog',
            'video_path' => 'https://www.youtube.com/watch?v=fsCIw1y5quU',
        ]);

        CarReview::query()->create([
            'car_id' => $car->id,
            'import_index' => 0,
            'type' => 'good',
            'value' => 'Comfortable ride',
        ]);

        CarConfigurationGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Base',
            'order' => 1,
            'import_index' => 0,
        ]);

        foreach ([
            '/tesla/model-y',
            '/tesla/model-y/crash-test/',
            '/tesla/model-y/test-drive/',
            '/tesla/model-y/reviews/',
            '/tesla/model-y/equipment-1/',
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('ВСЕ ФОТО')
                ->assertSee('/tesla/model-y/photo/', false);
        }
    }

    public function test_photo_page_returns_404_when_car_has_no_photos(): void
    {
        [$brand, $car] = $this->createBrandAndCar();

        $this->get('/tesla/model-y/photo/')
            ->assertNotFound();
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
