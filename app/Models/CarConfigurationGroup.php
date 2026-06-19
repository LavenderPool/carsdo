<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PublicContentObserver::class])]
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
}
