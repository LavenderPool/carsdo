<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class City extends Model
{
    public function carDealers(): HasMany
    {
        return $this->hasMany(CarDealer::class);
    }
}
