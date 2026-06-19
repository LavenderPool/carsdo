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
        Schema::table('car_configurations', function (Blueprint $table) {
            $table->unsignedInteger('local_id')->nullable()->after('id');
        });
        if (Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->dropUnique('ccec_group_import_unique');
            });
        }
        Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
            $table->dropColumn('car_configuration_group_id');
        });
        if (! Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->unique(
                    ['car_configuration_id', 'import_index'],
                    'ccec_configuration_import_unique',
                );
            });
        }
        Schema::table('cars', function (Blueprint $table) {
            $table->json('versions')->nullable()->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->dropUnique('ccec_configuration_import_unique');
            });
        }

        Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('car_configuration_group_id')->nullable()->after('id');
        });

        if (! Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->unique(
                    ['car_configuration_group_id', 'import_index'],
                    'ccec_group_import_unique',
                );
            });
        }

        Schema::table('car_configurations', function (Blueprint $table) {
            $table->dropColumn('local_id');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('versions');
        });
    }
};
