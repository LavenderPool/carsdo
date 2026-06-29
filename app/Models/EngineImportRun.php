<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineImportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'original_file_name',
        'file_path',
        'file_size',
        'message',
        'current_stage',
        'total_engines',
        'processed_engines',
        'stats_new',
        'stats_updated',
        'stats_unchanged',
        'stats_processed',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'total_engines' => 'integer',
        'processed_engines' => 'integer',
        'stats_new' => 'integer',
        'stats_updated' => 'integer',
        'stats_unchanged' => 'integer',
        'stats_processed' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
