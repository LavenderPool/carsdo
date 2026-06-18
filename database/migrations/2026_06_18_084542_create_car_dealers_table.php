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
        Schema::create('car_dealers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dealer_id')->constrained()->cascadeOnDelete();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_official')->default(false);
            $table->timestamps();

            $table->unique(['car_id', 'dealer_id', 'city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_dealers');
    }
};
