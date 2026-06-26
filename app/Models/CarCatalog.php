<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable([
    'name',
    'slug',
    'description',
    'is_published',
    'sort_order',
    'filters_json',
    'seo_title',
    'seo_description',
    'seo_h1',
    'seo_og_image',
    'seo_canonical_url',
    'seo_robots',
])]
class CarCatalog extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'filters_json' => 'array',
    ];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'car_catalog_car')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
