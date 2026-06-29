<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_configurations', function (Blueprint $table): void {
            $table->foreignId('engine_id')
                ->nullable()
                ->after('engine_type')
                ->constrained('engines')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('car_configurations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('engine_id');
        });
    }
};
