<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'year', 'rating', 'video_path'])]
class CarCrashTest extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'rating' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
