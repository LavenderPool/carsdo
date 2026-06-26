<?php

namespace App\Jobs;

use App\Support\Media\MediaVariantService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ConvertCarWebpChunkJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    /**
     * @param  array<int, array{owner_type: class-string, owner_id: int, source_path: string}>  $items
     */
    public function __construct(
        public array $items,
    ) {
    }

    public function handle(MediaVariantService $mediaVariantService): void
    {
        foreach ($this->items as $item) {
            $mediaVariantService->ensureWebpVariant(
                $item['source_path'],
                $item['owner_type'],
                $item['owner_id'],
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Cache::forget(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY);
    }
}
