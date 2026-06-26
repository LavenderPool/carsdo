<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'blog_seo_title')) {
                $table->text('blog_seo_title')->nullable()->after('cars_photo_seo_robots');
            }

            if (! Schema::hasColumn('settings', 'blog_seo_description')) {
                $table->text('blog_seo_description')->nullable()->after('blog_seo_title');
            }

            if (! Schema::hasColumn('settings', 'blog_seo_h1')) {
                $table->text('blog_seo_h1')->nullable()->after('blog_seo_description');
            }

            if (! Schema::hasColumn('settings', 'blog_seo_og_image')) {
                $table->text('blog_seo_og_image')->nullable()->after('blog_seo_h1');
            }

            if (! Schema::hasColumn('settings', 'blog_seo_canonical_url')) {
                $table->text('blog_seo_canonical_url')->nullable()->after('blog_seo_og_image');
            }

            if (! Schema::hasColumn('settings', 'blog_seo_robots')) {
                $table->text('blog_seo_robots')->nullable()->after('blog_seo_canonical_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'blog_seo_title',
                'blog_seo_description',
                'blog_seo_h1',
                'blog_seo_og_image',
                'blog_seo_canonical_url',
                'blog_seo_robots',
            ]);
        });
    }
};
