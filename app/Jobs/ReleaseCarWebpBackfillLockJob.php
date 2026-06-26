<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class ReleaseCarWebpBackfillLockJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Cache::forget(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY);
    }
}
