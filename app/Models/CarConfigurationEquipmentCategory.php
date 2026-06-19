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

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(CarConfiguration::class, 'car_configuration_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CarConfigurationEquipment::class, 'car_configuration_equipment_category_id');
    }
}
