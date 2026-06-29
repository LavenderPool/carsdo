<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engine_import_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('original_file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('message')->nullable();
            $table->string('current_stage')->nullable();
            $table->unsignedInteger('total_engines')->default(0);
            $table->unsignedInteger('processed_engines')->default(0);
            $table->unsignedInteger('stats_new')->default(0);
            $table->unsignedInteger('stats_updated')->default(0);
            $table->unsignedInteger('stats_unchanged')->default(0);
            $table->unsignedInteger('stats_processed')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engine_import_runs');
    }
};
