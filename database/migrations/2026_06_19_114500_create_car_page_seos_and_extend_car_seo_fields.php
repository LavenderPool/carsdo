<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_page_seos', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('name');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('h1')->nullable();
            $table->text('og_image')->nullable();
            $table->text('canonical_url')->nullable();
            $table->text('robots')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('car_page_seos')->insert([
            ['page_key' => 'car_index', 'name' => 'Основная страница автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_equipment', 'name' => 'Страница комплектаций автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_dealer', 'name' => 'Страница дилеров автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_reviews', 'name' => 'Страница отзывов автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_crash_test', 'name' => 'Страница краш-теста автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_test_drive', 'name' => 'Страница тест-драйва автомобиля', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'car_photo', 'name' => 'Страница фото автомобиля', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('cars', function (Blueprint $table) {
            foreach (['dealer_seo', 'photo_seo'] as $prefix) {
                if (! Schema::hasColumn('cars', "{$prefix}_title")) {
                    $table->text("{$prefix}_title")->nullable()->after('test_drive_seo_robots');
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
            $table->dropColumn([
                'dealer_seo_title',
                'dealer_seo_description',
                'dealer_seo_h1',
                'dealer_seo_og_image',
                'dealer_seo_canonical_url',
                'dealer_seo_robots',
                'photo_seo_title',
                'photo_seo_description',
                'photo_seo_h1',
                'photo_seo_og_image',
                'photo_seo_canonical_url',
                'photo_seo_robots',
            ]);
        });

        Schema::dropIfExists('car_page_seos');
    }
};
