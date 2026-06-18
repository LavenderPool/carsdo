<?php

namespace App\Support\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class SiteCache
{
    private const VERSION_KEY = 'site:cache:version';

    private const DEFAULT_TTL = 86400;

    public static function version(): int
    {
        $version = Cache::get(self::VERSION_KEY);

        if (! is_numeric($version)) {
            Cache::forever(self::VERSION_KEY, 1);

            return 1;
        }

        return (int) $version;
    }

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public static function remember(string $key, Closure $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return Cache::remember(self::key($key), $ttl, $callback);
    }

    public static function flush(): void
    {
        if (Cache::get(self::VERSION_KEY) === null) {
            Cache::forever(self::VERSION_KEY, 1);
        }

        Cache::increment(self::VERSION_KEY);
    }

    private static function key(string $key): string
    {
        return 'site:v'.self::version().':'.$key;
    }
}
