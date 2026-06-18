<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTestDrivePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_test_drive_page_returns_ok_and_shows_all_test_drives(): void
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

        $car->testDrives()->createMany([
            [
                'import_index' => 0,
                'author' => 'Autoblog',
                'video_path' => 'https://www.youtube.com/watch?v=fsCIw1y5quU',
            ],
            [
                'import_index' => 1,
                'author' => 'За рулем',
                'video_path' => '5eX2r2K6A1A',
            ],
        ]);

        $this->get('/tesla/model-y/test-drive/')
            ->assertOk()
            ->assertSee('Autoblog')
            ->assertSee('За рулем');
    }

    public function test_test_drive_page_returns_404_when_car_has_no_test_drives(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Model 3',
            'slug' => 'model-3',
            'year' => '2026',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        $this->get('/tesla/model-3/test-drive/')
            ->assertNotFound();
    }

    public function test_test_drive_page_returns_404_for_brand_and_car_mismatch(): void
    {
        $brandA = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);
        $brandB = Brand::query()->create([
            'name' => 'BMW',
            'slug' => 'bmw',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brandB->id,
            'name' => 'iX',
            'slug' => 'ix',
            'year' => '2026',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        $car->testDrives()->create([
            'import_index' => 0,
            'author' => 'AutoReview',
            'video_path' => 'https://youtu.be/fsCIw1y5quU',
        ]);

        $this->get('/'.$brandA->slug.'/'.$car->slug.'/test-drive/')
            ->assertNotFound();
    }
}
