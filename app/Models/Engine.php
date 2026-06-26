<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'brand_id',
    'name',
    'slug',
    'engine_url',
    'engine_type',
    'displacement_cc',
    'max_horsepower',
    'max_power_output_at_rpm',
    'max_torque_at_rpm',
    'valves_per_cylinder',
    'compression_ratio',
    'cylinder_bore_mm',
    'piston_stroke_mm',
    'valvetrain',
    'recommended_fuel_type',
    'fuel_consumption_l_per_100_km',
    'co2_emissions_g_per_km',
    'has_start_stop_system',
    'engine_notes',
    'page_text',
])]
class Engine extends Model
{
    use HasFactory;

    protected $casts = [
        'brand_id' => 'integer',
        'has_start_stop_system' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(CarConfiguration::class);
    }
}
