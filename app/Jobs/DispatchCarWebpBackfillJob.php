<?php

namespace App\Jobs;

use App\Support\Media\CarWebpBackfillService;
use App\Support\Media\MediaVariantService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class DispatchCarWebpBackfillJob implements ShouldQueue
{
    use Queueable;

    public const RUNNING_CACHE_KEY = 'admin:danger:webp-convert:running';

    private const CHUNK_SIZE = 50;

    public int $timeout = 0;

    public function handle(
        CarWebpBackfillService $backfillService,
        MediaVariantService $mediaVariantService,
    ): void {
        $chunks = $backfillService->pendingChunks(self::CHUNK_SIZE);

        if ($chunks === []) {
            Cache::forget(self::RUNNING_CACHE_KEY);

            return;
        }

        if (config('queue.default') === 'sync') {
            try {
                foreach ($chunks as $chunk) {
                    (new ConvertCarWebpChunkJob($chunk))->handle($mediaVariantService);
                }
            } finally {
                Cache::forget(self::RUNNING_CACHE_KEY);
            }

            return;
        }

        Bus::chain([
            ...array_map(
                fn (array $chunk): ConvertCarWebpChunkJob => new ConvertCarWebpChunkJob($chunk),
                $chunks,
            ),
            new \App\Jobs\ReleaseCarWebpBackfillLockJob(),
        ])->dispatch();
    }

    public function failed(): void
    {
        Cache::forget(self::RUNNING_CACHE_KEY);
    }
}
