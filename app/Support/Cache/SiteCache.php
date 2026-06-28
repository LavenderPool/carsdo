<?php

namespace App\Support\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class SiteCache
{
    private const VERSION_KEY = 'site:cache:version';

    private const PAYLOAD_KEY = '__site_cache_payload';

    private const CURRENT_VERSION = 2;

    private const DEFAULT_TTL = 86400;

    public static function version(): int
    {
        $version = Cache::get(self::VERSION_KEY);

        if (! is_numeric($version) || (int) $version < self::CURRENT_VERSION) {
            Cache::forever(self::VERSION_KEY, self::CURRENT_VERSION);

            return self::CURRENT_VERSION;
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
        $cacheKey = self::key($key);
        $missing = new \stdClass();

        try {
            $payload = Cache::get($cacheKey, $missing);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $payload = $missing;
        }

        if ($payload !== $missing) {
            return self::serializer()->restore(
                is_array($payload) && array_key_exists(self::PAYLOAD_KEY, $payload)
                    ? $payload[self::PAYLOAD_KEY]
                    : $payload
            );
        }

        $value = $callback();

        Cache::put($cacheKey, [
            self::PAYLOAD_KEY => self::serializer()->prepare($value),
        ], $ttl);

        return $value;
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

    private static function serializer(): SiteCacheValueSerializer
    {
        return app(SiteCacheValueSerializer::class);
    }
}
