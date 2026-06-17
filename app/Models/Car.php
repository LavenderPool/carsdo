<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'brand_id',
    'is_electric_car',
    'is_soon',
    'is_another_models',
    'name',
    'slug',
    'year',
    'cover_path',
    'start_price',
    'end_price',
    'official_site',
    'views_count',
])]
class Car extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_electric_car' => 'boolean',
        'is_soon' => 'boolean',
        'is_another_models' => 'boolean',
        'start_price' => 'integer',
        'end_price' => 'integer',
        'views_count' => 'integer',
    ];

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('views_count');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function crashTest(): HasOne
    {
        return $this->hasOne(CarCrashTest::class);
    }

    public function testDrives(): HasMany
    {
        return $this->hasMany(CarTestDrive::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CarReview::class);
    }

    public function configurationGroups(): HasMany
    {
        return $this->hasMany(CarConfigurationGroup::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(CarConfiguration::class);
    }

    public function photoGroups(): HasMany
    {
        return $this->hasMany(CarPhotoGroup::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CarPhoto::class);
    }

    public function coverUrl(): string
    {
        $brandSlug = $this->brand?->slug;

        if (is_string($brandSlug) && $brandSlug !== '' && $this->slug !== '') {
            return "/covers/{$brandSlug}/{$this->slug}/cover.jpg";
        }

        if (is_string($this->cover_path) && $this->cover_path !== '') {
            return str_starts_with($this->cover_path, '/')
                ? $this->cover_path
                : '/storage/'.ltrim($this->cover_path, '/');
        }

        return '/assets/img/start.png';
    }
}
