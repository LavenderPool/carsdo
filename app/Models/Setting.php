<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'brand_name',
        'favicon_path',
        'seo_title_suffix',
        'seo_default_description',
        'seo_default_robots',
        'seo_default_og_image',
        'home_seo_title',
        'home_seo_description',
        'home_seo_h1',
        'home_seo_og_image',
        'home_seo_canonical_url',
        'home_seo_robots',
        'new_cars_seo_title',
        'new_cars_seo_description',
        'new_cars_seo_h1',
        'new_cars_seo_og_image',
        'new_cars_seo_canonical_url',
        'new_cars_seo_robots',
        'electric_cars_seo_title',
        'electric_cars_seo_description',
        'electric_cars_seo_h1',
        'electric_cars_seo_og_image',
        'electric_cars_seo_canonical_url',
        'electric_cars_seo_robots',
        'crash_tests_seo_title',
        'crash_tests_seo_description',
        'crash_tests_seo_h1',
        'crash_tests_seo_og_image',
        'crash_tests_seo_canonical_url',
        'crash_tests_seo_robots',
        'test_drives_seo_title',
        'test_drives_seo_description',
        'test_drives_seo_h1',
        'test_drives_seo_og_image',
        'test_drives_seo_canonical_url',
        'test_drives_seo_robots',
        'cars_photo_seo_title',
        'cars_photo_seo_description',
        'cars_photo_seo_h1',
        'cars_photo_seo_og_image',
        'cars_photo_seo_canonical_url',
        'cars_photo_seo_robots',
        'blog_seo_title',
        'blog_seo_description',
        'blog_seo_h1',
        'blog_seo_og_image',
        'blog_seo_canonical_url',
        'blog_seo_robots',
    ];
}
