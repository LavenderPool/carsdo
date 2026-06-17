<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
