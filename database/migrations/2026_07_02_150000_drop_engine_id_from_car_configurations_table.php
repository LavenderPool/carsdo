<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('car_configurations', 'engine_id')) {
            return;
        }

        try {
            Schema::table('car_configurations', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('engine_id');
            });
        } catch (\Throwable) {
            if (! Schema::hasColumn('car_configurations', 'engine_id')) {
                return;
            }

            try {
                Schema::table('car_configurations', function (Blueprint $table): void {
                    $table->dropColumn('engine_id');
                });
            } catch (\Throwable) {
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('car_configurations', 'engine_id')) {
            return;
        }

        try {
            Schema::table('car_configurations', function (Blueprint $table): void {
                $table->foreignId('engine_id')
                    ->nullable()
                    ->after('engine_type')
                    ->constrained('engines')
                    ->nullOnDelete();
            });
        } catch (\Throwable) {
        }
    }
};
