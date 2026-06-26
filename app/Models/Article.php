<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use App\Support\Media\MediaPath;
use App\Support\Media\MediaVariantService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable([
    'title',
    'slug',
    'excerpt',
    'body',
    'body_json',
    'cover_path',
    'is_published',
    'published_at',
    'views_count',
    'seo_title',
    'seo_description',
    'seo_h1',
    'seo_og_image',
    'seo_canonical_url',
    'seo_robots',
])]
class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'body_json' => 'array',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function coverUrl(bool $generateIfMissing = true): string
    {
        if (is_string($this->cover_path) && $this->cover_path !== '') {
            $fallbackUrl = $this->resolveMediaUrl($this->cover_path);

            return app(MediaVariantService::class)->resolvePreferredUrl(
                $this->cover_path,
                $fallbackUrl,
                ! MediaPath::isExternal($this->cover_path) && $generateIfMissing,
                self::class,
                $this->id,
            ) ?? $fallbackUrl;
        }

        return '/assets/img/start.png';
    }

    private function resolveMediaUrl(string $path): string
    {
        if (MediaPath::isExternal($path)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return MediaPath::publicUrl($path) ?? $path;
    }
}
