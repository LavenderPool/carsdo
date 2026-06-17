<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'car_id',
    'car_configuration_group_id',
    'import_index',
    'price',
    'engine_type',
    'engine_capacity',
    'horsepower',
    'transmission',
    'drive_type',
    'fuel_city',
    'fuel_highway',
    'fuel_combined',
    'acceleration',
    'speed',
])]
class CarConfiguration extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'import_index' => 'integer',
        'price' => 'integer',
        'engine_capacity' => 'decimal:2',
        'horsepower' => 'integer',
        'fuel_city' => 'decimal:1',
        'fuel_highway' => 'decimal:1',
        'fuel_combined' => 'decimal:1',
        'acceleration' => 'decimal:1',
        'speed' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CarConfigurationGroup::class, 'car_configuration_group_id');
    }
}
