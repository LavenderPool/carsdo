<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'car_photo_group_id', 'photo_path'])]
class CarPhoto extends Model
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CarPhotoGroup::class, 'car_photo_group_id');
    }

    public function url(): string
    {
        $path = (string) $this->photo_path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return '/storage/'.ltrim($path, '/');
    }

    public function publicDiskPath(): ?string
    {
        $path = trim((string) $this->photo_path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, strlen('/storage/')), '/');
        }

        if (str_starts_with($path, 'storage/')) {
            return ltrim(substr($path, strlen('storage/')), '/');
        }

        if (str_starts_with($path, '/')) {
            return null;
        }

        return ltrim($path, '/');
    }
}
