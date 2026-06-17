<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'car_photo_group_id', 'photo_path'])]
class CarPhoto extends Model
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CarPhotoGroup::class, 'car_photo_group_id');
    }
}
