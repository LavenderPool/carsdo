<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'original_file_name',
        'file_path',
        'file_size',
        'message',
        'total_cars',
        'processed_cars',
        'stats_new',
        'stats_updated',
        'stats_unchanged',
        'stats_processed',
        'error_message',
        'started_at',
        'finished_at',
        'stop_requested_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'total_cars' => 'integer',
        'processed_cars' => 'integer',
        'stats_new' => 'integer',
        'stats_updated' => 'integer',
        'stats_unchanged' => 'integer',
        'stats_processed' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'stop_requested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
