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
        Schema::create('car_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('car_configuration_group_id');
            $table->string('price')->nullable();
            $table->string('engine_type')->nullable();
            $table->decimal('engine_capacity', 5,2)->nullable();
            $table->unsignedInteger('horsepower')->nullable();
            $table->string('drive_type')->nullable();
            $table->string('transmission')->nullable();
            $table->decimal('fuel_city', 4, 1)->nullable();
            $table->decimal('fuel_highway', 4, 1)->nullable();
            $table->decimal('fuel_combined', 4, 1)->nullable();
            $table->unsignedInteger('speed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_configurations');
    }
};
