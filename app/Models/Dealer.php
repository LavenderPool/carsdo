<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable(['name'])]
class Dealer extends Model
{
    public function carDealers(): HasMany
    {
        return $this->hasMany(CarDealer::class);
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'car_dealers')
            ->withPivot(['city_id', 'address', 'phone', 'website', 'is_official'])
            ->withTimestamps();
    }
}
