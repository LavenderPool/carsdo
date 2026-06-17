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
        Schema::create('car_configuration_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_configuration_id');
            $table->unsignedBigInteger('car_configuration_equipment_category_id');
            $table->string('value');
            $table->boolean('is_extension')->default(false);
            $table->unsignedInteger('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_configuration_equipment');
    }
};
