<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use App\Support\Media\MediaPath;
use App\Support\Media\MediaVariantService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[ObservedBy([PublicContentObserver::class])]
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

    public function mediaAliases(): MorphMany
    {
        return $this->morphMany(MediaAlias::class, 'owner');
    }

    public function url(bool $generateIfMissing = true): string
    {
        $path = (string) $this->photo_path;
        $fallbackUrl = $this->resolveMediaUrl($path);

        if ($fallbackUrl === null) {
            return $path;
        }

        return app(MediaVariantService::class)->resolvePreferredUrl(
            $this->photo_path,
            $fallbackUrl,
            $generateIfMissing,
            self::class,
            $this->id,
        ) ?? $fallbackUrl;
    }

    public function originalUrl(): string
    {
        $path = (string) $this->photo_path;
        $fallbackUrl = $this->resolveMediaUrl($path);

        return $fallbackUrl ?? $path;
    }

    public function publicDiskPath(): ?string
    {
        return MediaPath::publicDiskPath($this->photo_path);
    }

    private function resolveMediaUrl(string $path): ?string
    {
        if (MediaPath::isExternal($path)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return MediaPath::publicUrl($path);
    }
}
