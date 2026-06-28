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
#[Fillable(['car_id', 'import_index', 'rating', 'full_name', 'photo_path', 'text'])]
class CarOwnerReview extends Model
{
    protected $casts = [
        'import_index' => 'integer',
        'rating' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function mediaAliases(): MorphMany
    {
        return $this->morphMany(MediaAlias::class, 'owner');
    }

    public function photoUrl(bool $generateIfMissing = true): ?string
    {
        $path = (string) $this->photo_path;

        if ($path === '') {
            return null;
        }

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

    public function originalPhotoUrl(): ?string
    {
        $path = (string) $this->photo_path;

        if ($path === '') {
            return null;
        }

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
