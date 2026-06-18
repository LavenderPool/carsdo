<?php

namespace App\Support;

use Illuminate\Support\Facades\Cookie;

class RecentViews
{
    private const COOKIE_NAME = 'recent_views_v1';

    private const MAX_RECENT_VIEWS = 50;

    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    /**
     * Registers a view for the given key and returns whether it was a new view
     * (i.e. it had not been seen recently before this call).
     */
    public function remember(string $key): bool
    {
        $recentViews = $this->readRecentViews();

        $alreadyCounted = in_array($key, $recentViews, true);

        $recentViews = array_values(array_filter(
            $recentViews,
            static fn (string $value): bool => $value !== $key,
        ));
        array_unshift($recentViews, $key);
        $recentViews = array_slice($recentViews, 0, self::MAX_RECENT_VIEWS);

        Cookie::queue(cookie(
            self::COOKIE_NAME,
            json_encode($recentViews, JSON_UNESCAPED_SLASHES),
            self::COOKIE_LIFETIME_MINUTES,
        ));

        return ! $alreadyCounted;
    }

    /**
     * @return array<int, string>
     */
    private function readRecentViews(): array
    {
        $rawRecentViews = request()->cookie(self::COOKIE_NAME);

        if (! is_string($rawRecentViews) || $rawRecentViews === '') {
            return [];
        }

        $decodedRecentViews = json_decode($rawRecentViews, true);

        if (! is_array($decodedRecentViews)) {
            return [];
        }

        return array_values(array_filter(
            $decodedRecentViews,
            static fn (mixed $value): bool => is_string($value) && $value !== '',
        ));
    }
}
