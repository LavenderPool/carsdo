<?php

namespace App\Support\Media;

use App\Models\Car;
use App\Models\CarPhoto;
use Illuminate\Support\Facades\Storage;

class CarMediaStorage
{
    public static function deletePhotoFile(CarPhoto $photo): void
    {
        $path = $photo->publicDiskPath();

        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @param  iterable<int, CarPhoto>  $photos
     */
    public static function deletePhotoFiles(iterable $photos): void
    {
        foreach ($photos as $photo) {
            self::deletePhotoFile($photo);
        }
    }

    public static function deleteCarDirectories(Car $car): void
    {
        $car->loadMissing('brand:id,slug');

        $brandSlug = $car->brand?->slug;
        $carSlug = $car->slug;

        if (!is_string($brandSlug) || $brandSlug === '' || !is_string($carSlug) || $carSlug === '') {
            return;
        }

        Storage::disk('public')->deleteDirectory("images/{$brandSlug}/{$carSlug}");
        Storage::disk('public')->deleteDirectory("covers/{$brandSlug}/{$carSlug}");
    }
}
