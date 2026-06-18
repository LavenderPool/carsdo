<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'seo_title_suffix')) {
                $table->string('seo_title_suffix')->nullable()->after('favicon_path');
            }

            if (! Schema::hasColumn('settings', 'seo_default_description')) {
                $table->text('seo_default_description')->nullable()->after('seo_title_suffix');
            }

            if (! Schema::hasColumn('settings', 'seo_default_robots')) {
                $table->text('seo_default_robots')->nullable()->after('seo_default_description');
            }

            if (! Schema::hasColumn('settings', 'seo_default_og_image')) {
                $table->text('seo_default_og_image')->nullable()->after('seo_default_robots');
            }

            foreach (['home', 'new_cars', 'electric_cars', 'crash_tests', 'test_drives', 'cars_photo'] as $prefix) {
                if (! Schema::hasColumn('settings', "{$prefix}_seo_title")) {
                    $table->text("{$prefix}_seo_title")->nullable();
                }

                if (! Schema::hasColumn('settings', "{$prefix}_seo_description")) {
                    $table->text("{$prefix}_seo_description")->nullable();
                }

                if (! Schema::hasColumn('settings', "{$prefix}_seo_h1")) {
                    $table->text("{$prefix}_seo_h1")->nullable();
                }

                if (! Schema::hasColumn('settings', "{$prefix}_seo_og_image")) {
                    $table->text("{$prefix}_seo_og_image")->nullable();
                }

                if (! Schema::hasColumn('settings', "{$prefix}_seo_canonical_url")) {
                    $table->text("{$prefix}_seo_canonical_url")->nullable();
                }

                if (! Schema::hasColumn('settings', "{$prefix}_seo_robots")) {
                    $table->text("{$prefix}_seo_robots")->nullable();
                }
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            if (! Schema::hasColumn('brands', 'seo_title')) {
                $table->text('seo_title')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('brands', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }

            if (! Schema::hasColumn('brands', 'seo_h1')) {
                $table->text('seo_h1')->nullable()->after('seo_description');
            }

            if (! Schema::hasColumn('brands', 'seo_og_image')) {
                $table->text('seo_og_image')->nullable()->after('seo_h1');
            }

            if (! Schema::hasColumn('brands', 'seo_canonical_url')) {
                $table->text('seo_canonical_url')->nullable()->after('seo_og_image');
            }

            if (! Schema::hasColumn('brands', 'seo_robots')) {
                $table->text('seo_robots')->nullable()->after('seo_canonical_url');
            }
        });

        Schema::table('cars', function (Blueprint $table) {
            foreach (['seo', 'equipment_seo', 'reviews_seo', 'crash_test_seo', 'test_drive_seo'] as $prefix) {
                if (! Schema::hasColumn('cars', "{$prefix}_title")) {
                    $table->text("{$prefix}_title")->nullable()->after('official_site');
                }

                if (! Schema::hasColumn('cars', "{$prefix}_description")) {
                    $table->text("{$prefix}_description")->nullable()->after("{$prefix}_title");
                }

                if (! Schema::hasColumn('cars', "{$prefix}_h1")) {
                    $table->text("{$prefix}_h1")->nullable()->after("{$prefix}_description");
                }

                if (! Schema::hasColumn('cars', "{$prefix}_og_image")) {
                    $table->text("{$prefix}_og_image")->nullable()->after("{$prefix}_h1");
                }

                if (! Schema::hasColumn('cars', "{$prefix}_canonical_url")) {
                    $table->text("{$prefix}_canonical_url")->nullable()->after("{$prefix}_og_image");
                }

                if (! Schema::hasColumn('cars', "{$prefix}_robots")) {
                    $table->text("{$prefix}_robots")->nullable()->after("{$prefix}_canonical_url");
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            foreach (['seo', 'equipment_seo', 'reviews_seo', 'crash_test_seo', 'test_drive_seo'] as $prefix) {
                $table->dropColumn([
                    "{$prefix}_title",
                    "{$prefix}_description",
                    "{$prefix}_h1",
                    "{$prefix}_og_image",
                    "{$prefix}_canonical_url",
                    "{$prefix}_robots",
                ]);
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'seo_h1',
                'seo_og_image',
                'seo_canonical_url',
                'seo_robots',
            ]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
