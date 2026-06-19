<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPageSeo extends Model
{
    protected $fillable = [
        'page_key',
        'name',
        'title',
        'description',
        'h1',
        'og_image',
        'canonical_url',
        'robots',
    ];
}
