<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'import_index', 'author', 'video_path'])]
class CarTestDrive extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'import_index' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
