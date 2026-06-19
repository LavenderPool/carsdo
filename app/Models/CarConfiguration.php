<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable([
    'car_id',
    'car_configuration_group_id',
    'local_id',
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
        'local_id' => 'integer',
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

    public function equipmentCategories(): HasMany
    {
        return $this->hasMany(CarConfigurationEquipmentCategory::class, 'car_configuration_id');
    }
}
