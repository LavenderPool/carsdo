<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['car_id', 'name', 'order', 'import_index'])]
class CarConfigurationGroup extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'import_index' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(CarConfiguration::class);
    }

    public function equipmentCategories(): HasMany
    {
        return $this->hasMany(CarConfigurationEquipmentCategory::class);
    }
}
