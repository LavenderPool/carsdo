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
        if (Schema::hasIndex('cars', ['slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        }

        if (! Schema::hasIndex('cars', ['brand_id', 'slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->unique(['brand_id', 'slug'], 'cars_brand_id_slug_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('cars', ['brand_id', 'slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropUnique('cars_brand_id_slug_unique');
            });
        }

        if (! Schema::hasIndex('cars', ['slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }
};
