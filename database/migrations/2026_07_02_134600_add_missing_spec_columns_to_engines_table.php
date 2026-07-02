<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_filter([
            'air_charger',
            'cooling_system_capacity_l',
            'specific_power_kg_per_hp',
        ], fn (string $column): bool => ! Schema::hasColumn('engines', $column));

        if ($columns === []) {
            return;
        }

        Schema::table('engines', function (Blueprint $table) use ($columns): void {
            if (in_array('air_charger', $columns, true)) {
                $table->string('air_charger')->nullable();
            }

            if (in_array('cooling_system_capacity_l', $columns, true)) {
                $table->string('cooling_system_capacity_l')->nullable();
            }

            if (in_array('specific_power_kg_per_hp', $columns, true)) {
                $table->string('specific_power_kg_per_hp')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter([
            'air_charger',
            'cooling_system_capacity_l',
            'specific_power_kg_per_hp',
        ], fn (string $column): bool => Schema::hasColumn('engines', $column)));

        if ($columns === []) {
            return;
        }

        Schema::table('engines', function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }
};
