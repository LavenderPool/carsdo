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
        Schema::table('import_runs', function (Blueprint $table): void {
            $table->string('current_stage')->nullable()->after('message');
            $table->unsignedInteger('chunks_total')->nullable()->after('processed_cars');
            $table->unsignedInteger('chunks_processed')->default(0)->after('chunks_total');
            $table->unsignedInteger('current_chunk_index')->nullable()->after('chunks_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_runs', function (Blueprint $table): void {
            $table->dropColumn([
                'current_stage',
                'chunks_total',
                'chunks_processed',
                'current_chunk_index',
            ]);
        });
    }
};
