<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_owner_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->unsignedInteger('import_index')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('full_name');
            $table->string('photo_path')->nullable();
            $table->text('text');
            $table->timestamps();

            $table->unique(
                ['car_id', 'import_index'],
                'car_owner_reviews_car_id_import_index_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_owner_reviews');
    }
};
