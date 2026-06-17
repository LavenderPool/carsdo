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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->boolean('is_electric_car')->default(false);
            $table->boolean('is_soon')->default(false);
            $table->boolean('is_another_models')->default(false);
            $table->string('name');
            $table->string('slug');
            $table->string('year');
            $table->string('cover_path')->nullable();

            $table->string('start_price')->nullable();
            $table->string('end_price')->nullable();
            $table->text('official_site')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
