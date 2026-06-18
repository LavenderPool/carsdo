<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PublicContentObserver::class])]
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
