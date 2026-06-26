<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ConvertCarWebpChunkJob;
use App\Jobs\DispatchCarWebpBackfillJob;
use App\Jobs\ReleaseCarWebpBackfillLockJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class DispatchCarWebpBackfillJobTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cache::forget(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY);

        parent::tearDown();
    }

    public function test_dispatch_job_queues_chunk_chain_and_keeps_lock_until_finish(): void
    {
        Storage::fake('public');
        config(['queue.default' => 'database']);

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

        $group = CarPhotoGroup::query()->create([
            'car_id' => $car->id,
            'name' => 'Gallery',
        ]);

        $photo = CarPhoto::query()->create([
            'car_id' => $car->id,
            'car_photo_group_id' => $group->id,
            'photo_path' => 'images/audi/a6/front.png',
        ]);

        Storage::disk('public')->put('images/audi/a6/front.png', $this->fakePngBinary());
        Cache::put(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY, true, now()->addHour());

        Bus::shouldReceive('chain')
            ->once()
            ->with(Mockery::on(function (array $jobs) use ($photo): bool {
                return count($jobs) === 2
                    && $jobs[0] instanceof ConvertCarWebpChunkJob
                    && $jobs[0]->items === [[
                        'owner_type' => CarPhoto::class,
                        'owner_id' => $photo->id,
                        'source_path' => 'images/audi/a6/front.png',
                    ]]
                    && $jobs[1] instanceof ReleaseCarWebpBackfillLockJob;
            }))
            ->andReturn(new class
            {
                public function dispatch(): void
                {
                }
            });

        $job = new DispatchCarWebpBackfillJob();
        $job->handle(
            app(\App\Support\Media\CarWebpBackfillService::class),
            app(\App\Support\Media\MediaVariantService::class),
        );

        $this->assertTrue(Cache::has(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY));
    }

    public function test_dispatch_job_releases_lock_when_nothing_is_pending(): void
    {
        Storage::fake('public');
        config(['queue.default' => 'database']);
        Cache::put(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY, true, now()->addHour());
        Bus::shouldReceive('chain')->never();

        $job = new DispatchCarWebpBackfillJob();
        $job->handle(
            app(\App\Support\Media\CarWebpBackfillService::class),
            app(\App\Support\Media\MediaVariantService::class),
        );

        $this->assertFalse(Cache::has(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY));
    }

    private function fakePngBinary(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEElEQVQImWP8z4AATAxEcQAz0QEH1mUzKgAAAABJRU5ErkJggg==',
            true,
        ) ?: '';
    }
}
