<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DangerController;
use App\Jobs\DispatchCarWebpBackfillJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\MediaAlias;
use App\Support\Media\CarWebpBackfillService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Response as InertiaResponse;
use Tests\TestCase;

class DangerWebpConvertTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cache::forget(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY);

        parent::tearDown();
    }

    public function test_guest_cannot_open_or_apply_webp_convert(): void
    {
        $this->get('/admin/danger/webp-convert')
            ->assertRedirect(route('login'));

        $this->post('/admin/danger/webp-convert')
            ->assertRedirect(route('login'));
    }

    public function test_preview_shows_summary_for_photos_and_covers(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        $brand = $this->createBrand('Audi');

        $pendingCar = $this->createCar($brand, 'A6', 'a6', 'covers/audi/a6/manual-cover.jpg');
        $convertedCoverCar = $this->createCar($brand, 'Q5', 'q5');
        $skippedCoverCar = $this->createCar($brand, 'Q7', 'q7', 'covers/audi/q7/missing-cover.jpg');

        $photoGroup = $this->createPhotoGroup($pendingCar);

        $pendingPhoto = CarPhoto::query()->create([
            'car_id' => $pendingCar->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/audi/a6/front.png',
        ]);

        $convertedPhoto = CarPhoto::query()->create([
            'car_id' => $pendingCar->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/audi/a6/rear.png',
        ]);

        CarPhoto::query()->create([
            'car_id' => $pendingCar->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'https://cdn.example.com/audi/a6/side.png',
        ]);

        Storage::disk('public')->put('images/audi/a6/front.png', $this->fakePngBinary());
        Storage::disk('public')->put('images/audi/a6/rear.png', $this->fakePngBinary());
        Storage::disk('public')->put('covers/audi/a6/manual-cover.jpg', $this->fakePngBinary());
        Storage::disk('public')->put('covers/audi/q5/cover.jpg', $this->fakePngBinary());

        $this->createAlias(
            'images/audi/a6/rear.png',
            'images/audi/a6/variants/rear.webp',
            CarPhoto::class,
            $convertedPhoto->id,
        );

        $this->createAlias(
            'covers/audi/q5/cover.jpg',
            'covers/audi/q5/variants/cover.webp',
            Car::class,
            $convertedCoverCar->id,
        );

        $request = Request::create('/admin/danger/webp-convert', 'GET');
        $request->setLaravelSession(app('session.store'));

        $response = app(DangerController::class)->webpConvert(
            $request,
            app(CarWebpBackfillService::class),
        );

        $this->assertInstanceOf(InertiaResponse::class, $response);

        $preview = app(CarWebpBackfillService::class)->buildPreview();

        $this->assertSame(6, $preview['summary']['total']);
        $this->assertSame(4, $preview['summary']['eligible']);
        $this->assertSame(2, $preview['summary']['converted']);
        $this->assertSame(2, $preview['summary']['pending']);
        $this->assertSame(2, $preview['summary']['skipped']);
        $this->assertSame('photos', $preview['sources'][0]['key']);
        $this->assertSame(3, $preview['sources'][0]['total']);
        $this->assertSame(2, $preview['sources'][0]['eligible']);
        $this->assertSame(1, $preview['sources'][0]['converted']);
        $this->assertSame(1, $preview['sources'][0]['pending']);
        $this->assertSame(1, $preview['sources'][0]['skipped']);
        $this->assertSame('covers', $preview['sources'][1]['key']);
        $this->assertSame(3, $preview['sources'][1]['total']);
        $this->assertSame(2, $preview['sources'][1]['eligible']);
        $this->assertSame(1, $preview['sources'][1]['converted']);
        $this->assertSame(1, $preview['sources'][1]['pending']);
        $this->assertSame(1, $preview['sources'][1]['skipped']);
        $this->assertNotNull($pendingPhoto->fresh());
    }

    public function test_apply_queues_webp_backfill_job(): void
    {
        Queue::fake();
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        $brand = $this->createBrand('BMW');
        $car = $this->createCar($brand, 'i5', 'i5');
        $photoGroup = $this->createPhotoGroup($car);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/bmw/i5/front.png',
        ]);

        Storage::disk('public')->put('images/bmw/i5/front.png', $this->fakePngBinary());

        $this->actingAs($user)
            ->post(route('admin.danger.webp-convert.apply'))
            ->assertRedirect(route('admin.danger.webp-convert'))
            ->assertSessionHas('success', 'Конвертация поставлена в очередь для 1 изображений.');

        Queue::assertPushed(DispatchCarWebpBackfillJob::class, 1);
        $this->assertTrue(Cache::has(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY));
    }

    public function test_apply_does_not_queue_duplicate_run(): void
    {
        Queue::fake();
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        $brand = $this->createBrand('Mercedes-Benz');
        $car = $this->createCar($brand, 'EQE', 'eqe');
        $photoGroup = $this->createPhotoGroup($car);

        CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/mercedes-benz/eqe/front.png',
        ]);

        Storage::disk('public')->put('images/mercedes-benz/eqe/front.png', $this->fakePngBinary());
        Cache::put(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY, true, now()->addHour());

        $this->actingAs($user)
            ->post(route('admin.danger.webp-convert.apply'))
            ->assertRedirect(route('admin.danger.webp-convert'))
            ->assertSessionHas('warning', 'Конвертация уже запущена.');

        Queue::assertNothingPushed();
    }

    private function createBrand(string $name): Brand
    {
        return Brand::query()->create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'leave_from_russian' => false,
        ]);
    }

    private function createCar(Brand $brand, string $name, string $slug, ?string $coverPath = null): Car
    {
        return Car::query()->create([
            'brand_id' => $brand->id,
            'name' => $name,
            'slug' => $slug,
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
            'cover_path' => $coverPath,
        ]);
    }

    private function createPhotoGroup(Car $car): CarPhotoGroup
    {
        return CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Gallery',
        ]);
    }

    private function createAlias(string $sourcePath, string $aliasPath, string $ownerType, int $ownerId): void
    {
        Storage::disk('public')->put($aliasPath, 'webp');

        MediaAlias::query()->create([
            'disk' => 'public',
            'source_path' => $sourcePath,
            'variant' => 'webp',
            'alias_path' => $aliasPath,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'mime_type' => 'image/webp',
            'width' => 4,
            'height' => 4,
            'file_size' => 4,
            'quality' => 82,
        ]);
    }

    private function fakePngBinary(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEElEQVQImWP8z4AATAxEcQAz0QEH1mUzKgAAAABJRU5ErkJggg==',
            true,
        ) ?: '';
    }
}
