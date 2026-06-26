<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Storage;

class MediaPath
{
    public static function isExternal(?string $path): bool
    {
        $path = trim((string) $path);

        return $path !== '' && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'));
    }

    public static function publicDiskPath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || self::isExternal($path)) {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, strlen('/storage/')), '/');
        }

        if (str_starts_with($path, 'storage/')) {
            return ltrim(substr($path, strlen('storage/')), '/');
        }

        if (!str_starts_with($path, '/')) {
            return ltrim($path, '/');
        }

        $candidate = ltrim($path, '/');

        return Storage::disk('public')->exists($candidate) ? $candidate : null;
    }

    public static function publicUrl(?string $path): ?string
    {
        $path = self::publicDiskPath($path);

        if ($path === null) {
            return null;
        }

        return '/storage/'.ltrim($path, '/');
    }
}
