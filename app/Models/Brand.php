<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable([
    'name',
    'slug',
    'leave_from_russian',
    'seo_title',
    'seo_description',
    'seo_h1',
    'seo_og_image',
    'seo_canonical_url',
    'seo_robots',
    'views_count',
])]
class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'leave_from_russian' => 'boolean',
        'views_count' => 'integer',
    ];

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('views_count');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
