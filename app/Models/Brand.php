<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'leave_from_russian', 'views_count'])]
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
