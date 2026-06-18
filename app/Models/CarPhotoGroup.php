<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable(['car_id', 'name'])]
class CarPhotoGroup extends Model
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CarPhoto::class);
    }
}
