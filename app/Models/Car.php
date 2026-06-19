<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([PublicContentObserver::class])]
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
    'seo_title',
    'seo_description',
    'seo_h1',
    'seo_og_image',
    'seo_canonical_url',
    'seo_robots',
    'equipment_seo_title',
    'equipment_seo_description',
    'equipment_seo_h1',
    'equipment_seo_og_image',
    'equipment_seo_canonical_url',
    'equipment_seo_robots',
    'dealer_seo_title',
    'dealer_seo_description',
    'dealer_seo_h1',
    'dealer_seo_og_image',
    'dealer_seo_canonical_url',
    'dealer_seo_robots',
    'reviews_seo_title',
    'reviews_seo_description',
    'reviews_seo_h1',
    'reviews_seo_og_image',
    'reviews_seo_canonical_url',
    'reviews_seo_robots',
    'crash_test_seo_title',
    'crash_test_seo_description',
    'crash_test_seo_h1',
    'crash_test_seo_og_image',
    'crash_test_seo_canonical_url',
    'crash_test_seo_robots',
    'test_drive_seo_title',
    'test_drive_seo_description',
    'test_drive_seo_h1',
    'test_drive_seo_og_image',
    'test_drive_seo_canonical_url',
    'test_drive_seo_robots',
    'photo_seo_title',
    'photo_seo_description',
    'photo_seo_h1',
    'photo_seo_og_image',
    'photo_seo_canonical_url',
    'photo_seo_robots',
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

    public function carDealers(): HasMany
    {
        return $this->hasMany(CarDealer::class);
    }

    public function dealers(): BelongsToMany
    {
        return $this->belongsToMany(Dealer::class, 'car_dealers')
            ->withPivot(['city_id', 'address', 'phone', 'website', 'is_official'])
            ->withTimestamps();
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'car_dealers');
    }

    public function coverUrl(): string
    {
        if (is_string($this->cover_path) && $this->cover_path !== '') {
            return $this->resolveMediaUrl($this->cover_path);
        }

        $brandSlug = $this->brand?->slug;

        if (is_string($brandSlug) && $brandSlug !== '' && $this->slug !== '') {
            return "/covers/{$brandSlug}/{$this->slug}/cover.jpg";
        }

        return '/assets/img/start.png';
    }

    private function resolveMediaUrl(string $path): string
    {
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
}
