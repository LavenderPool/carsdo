<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ConvertCarWebpChunkJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\MediaAlias;
use App\Support\Media\MediaVariantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConvertCarWebpChunkJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_chunk_job_converts_only_pending_items(): void
    {
        if (!$this->supportsWebpEncoding()) {
            $this->markTestSkipped('WebP encoder is unavailable.');
        }

        Storage::fake('public');

        $brand = Brand::query()->create([
            'name' => 'Audi',
            'slug' => 'audi',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'A6',
            'slug' => 'a6',
            'year' => '2024',
            'is_electric_car' => false,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        $photoGroup = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Gallery',
        ]);

        $pendingPhoto = CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/audi/a6/front.png',
        ]);

        $convertedPhoto = CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/audi/a6/rear.png',
        ]);

        $webpPhoto = CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $photoGroup->id,
            'photo_path' => 'images/audi/a6/already.webp',
        ]);

        Storage::disk('public')->put('images/audi/a6/front.png', $this->fakePngBinary());
        Storage::disk('public')->put('images/audi/a6/rear.png', $this->fakePngBinary());
        Storage::disk('public')->put('images/audi/a6/already.webp', $this->fakeWebpBinary());

        $existingAlias = app(MediaVariantService::class)->ensureWebpVariant(
            $convertedPhoto->photo_path,
            CarPhoto::class,
            $convertedPhoto->id,
        );

        $this->assertNotNull($existingAlias);

        $job = new ConvertCarWebpChunkJob([
            [
                'owner_type' => CarPhoto::class,
                'owner_id' => $pendingPhoto->id,
                'source_path' => 'images/audi/a6/front.png',
            ],
            [
                'owner_type' => CarPhoto::class,
                'owner_id' => $convertedPhoto->id,
                'source_path' => 'images/audi/a6/rear.png',
            ],
            [
                'owner_type' => CarPhoto::class,
                'owner_id' => $webpPhoto->id,
                'source_path' => 'images/audi/a6/already.webp',
            ],
        ]);

        $job->handle(app(MediaVariantService::class));

        $this->assertDatabaseHas('media_aliases', [
            'source_path' => 'images/audi/a6/front.png',
            'variant' => 'webp',
            'owner_type' => CarPhoto::class,
            'owner_id' => $pendingPhoto->id,
        ]);

        $this->assertSame(
            1,
            MediaAlias::query()
                ->where('source_path', 'images/audi/a6/rear.png')
                ->where('variant', 'webp')
                ->count(),
        );

        $this->assertDatabaseMissing('media_aliases', [
            'source_path' => 'images/audi/a6/already.webp',
            'variant' => 'webp',
        ]);
    }

    private function fakePngBinary(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEElEQVQImWP8z4AATAxEcQAz0QEH1mUzKgAAAABJRU5ErkJggg==',
            true,
        ) ?: '';
    }

    private function fakeWebpBinary(): string
    {
        if (function_exists('imagewebp')) {
            $image = imagecreatefromstring($this->fakePngBinary());

            if ($image !== false) {
                ob_start();
                imagewebp($image, null, 82);
                $binary = ob_get_clean();
                imagedestroy($image);

                if (is_string($binary) && $binary !== '') {
                    return $binary;
                }
            }
        }

        if (class_exists('\\Imagick')) {
            $image = new \Imagick();
            $image->readImageBlob($this->fakePngBinary());
            $image->setImageFormat('webp');

            return $image->getImagesBlob();
        }

        return '';
    }

    private function supportsWebpEncoding(): bool
    {
        return function_exists('imagewebp') || class_exists('\\Imagick');
    }
}
