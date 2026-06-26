<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
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
    'is_published',
    'published_at',
    'sort_order',
    'seo_title',
    'seo_description',
    'seo_h1',
    'seo_og_image',
    'seo_canonical_url',
    'seo_robots',
])]
class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'body_json' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
