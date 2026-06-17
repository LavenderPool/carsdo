<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'car_configuration_group_id',
    'car_configuration_id',
    'name',
    'import_index',
])]
class CarConfigurationEquipmentCategory extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'import_index' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CarConfigurationGroup::class, 'car_configuration_group_id');
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(CarConfiguration::class, 'car_configuration_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CarConfigurationEquipment::class, 'car_configuration_equipment_category_id');
    }
}
