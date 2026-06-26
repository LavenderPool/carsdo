<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'disk',
    'source_path',
    'variant',
    'alias_path',
    'owner_type',
    'owner_id',
    'mime_type',
    'width',
    'height',
    'file_size',
    'quality',
])]
class MediaAlias extends Model
{
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
