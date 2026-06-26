<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('filters_json')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_h1')->nullable();
            $table->text('seo_og_image')->nullable();
            $table->text('seo_canonical_url')->nullable();
            $table->string('seo_robots')->nullable();
            $table->timestamps();
        });

        Schema::create('car_catalog_car', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_catalog_id')->constrained('car_catalogs')->cascadeOnDelete();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['car_catalog_id', 'car_id']);
            $table->index(['car_catalog_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_catalog_car');
        Schema::dropIfExists('car_catalogs');
    }
};
