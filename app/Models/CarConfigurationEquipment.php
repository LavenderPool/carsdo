<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable([
    'car_configuration_id',
    'car_configuration_equipment_category_id',
    'import_index',
    'value',
    'is_extension',
    'price',
])]
class CarConfigurationEquipment extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'import_index' => 'integer',
        'is_extension' => 'boolean',
        'price' => 'integer',
    ];

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(CarConfiguration::class, 'car_configuration_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarConfigurationEquipmentCategory::class, 'car_configuration_equipment_category_id');
    }
}
