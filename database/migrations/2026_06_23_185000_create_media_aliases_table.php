<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('source_path');
            $table->string('variant', 32);
            $table->string('alias_path');
            $table->string('owner_type')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedTinyInteger('quality')->nullable();
            $table->timestamps();

            $table->unique(['disk', 'source_path', 'variant']);
            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_aliases');
    }
};
