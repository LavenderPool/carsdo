<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engines', function (Blueprint $table): void {
            $table->string('air_charger')->nullable();
            $table->string('cooling_system_capacity_l')->nullable();
            $table->string('specific_power_kg_per_hp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('engines', function (Blueprint $table): void {
            $table->dropColumn([
                'air_charger',
                'cooling_system_capacity_l',
                'specific_power_kg_per_hp',
            ]);
        });
    }
};
