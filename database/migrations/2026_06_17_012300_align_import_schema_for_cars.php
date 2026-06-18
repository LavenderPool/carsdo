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
        if (! Schema::hasIndex('brands', ['slug'], 'unique')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        Schema::table('cars', function (Blueprint $table) {
            $table->string('year')->nullable()->change();
        });

        if (! Schema::hasIndex('cars', ['slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        Schema::table('car_crash_tests', function (Blueprint $table) {
            if (! Schema::hasColumn('car_crash_tests', 'video_path')) {
                $table->text('video_path')->nullable()->after('rating');
            }
        });

        if (! Schema::hasIndex('car_crash_tests', ['car_id'], 'unique')) {
            Schema::table('car_crash_tests', function (Blueprint $table) {
                $table->unique('car_id');
            });
        }

        Schema::table('car_test_drives', function (Blueprint $table) {
            if (! Schema::hasColumn('car_test_drives', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('car_id');
            }
        });

        if (! Schema::hasIndex('car_test_drives', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_test_drives', function (Blueprint $table) {
                $table->unique(
                    ['car_id', 'import_index'],
                    'car_test_drives_car_id_import_index_unique',
                );
            });
        }

        Schema::table('car_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('car_reviews', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('car_id');
            }
        });

        if (! Schema::hasIndex('car_reviews', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_reviews', function (Blueprint $table) {
                $table->unique(
                    ['car_id', 'import_index'],
                    'car_reviews_car_id_import_index_unique',
                );
            });
        }

        Schema::table('car_configuration_groups', function (Blueprint $table) {
            if (! Schema::hasColumn('car_configuration_groups', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('order');
            }
        });

        if (! Schema::hasIndex('car_configuration_groups', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_groups', function (Blueprint $table) {
                $table->unique(
                    ['car_id', 'import_index'],
                    'car_configuration_groups_car_id_import_index_unique',
                );
            });
        }

        Schema::table('car_configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('car_configurations', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('car_configuration_group_id');
            }

            if (! Schema::hasColumn('car_configurations', 'acceleration')) {
                $table->decimal('acceleration', 4, 1)->nullable()->after('fuel_combined');
            }
        });

        if (! Schema::hasIndex('car_configurations', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configurations', function (Blueprint $table) {
                $table->unique(
                    ['car_configuration_group_id', 'import_index'],
                    'car_configurations_group_id_import_index_unique',
                );
            });
        }

        Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('car_configuration_equipment_categories', 'car_configuration_group_id')) {
                $table->unsignedBigInteger('car_configuration_group_id')->nullable()->after('id');
            }

            $table->unsignedBigInteger('car_configuration_id')->nullable()->change();

            if (! Schema::hasColumn('car_configuration_equipment_categories', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('name');
            }
        });

        if (! Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->unique(
                    ['car_configuration_group_id', 'import_index'],
                    'ccec_group_import_unique',
                );
            });
        }

        Schema::table('car_configuration_equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('car_configuration_id')->nullable()->change();

            if (! Schema::hasColumn('car_configuration_equipment', 'import_index')) {
                $table->unsignedInteger('import_index')->nullable()->after('car_configuration_equipment_category_id');
            }

            $table->string('value')->nullable()->change();
        });

        if (! Schema::hasIndex('car_configuration_equipment', ['car_configuration_equipment_category_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment', function (Blueprint $table) {
                $table->unique(
                    ['car_configuration_equipment_category_id', 'import_index'],
                    'car_configuration_equipment_category_id_import_index_unique',
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('car_configuration_equipment', ['car_configuration_equipment_category_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment', function (Blueprint $table) {
                $table->dropUnique('car_configuration_equipment_category_id_import_index_unique');
            });
        }

        Schema::table('car_configuration_equipment', function (Blueprint $table) {
            if (Schema::hasColumn('car_configuration_equipment', 'import_index')) {
                $table->dropColumn('import_index');
            }

            $table->unsignedBigInteger('car_configuration_id')->nullable(false)->change();
            $table->string('value')->nullable(false)->change();
        });

        if (Schema::hasIndex('car_configuration_equipment_categories', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
                $table->dropUnique('ccec_group_import_unique');
            });
        }

        Schema::table('car_configuration_equipment_categories', function (Blueprint $table) {
            $columnsToDrop = array_values(array_filter([
                Schema::hasColumn('car_configuration_equipment_categories', 'car_configuration_group_id') ? 'car_configuration_group_id' : null,
                Schema::hasColumn('car_configuration_equipment_categories', 'import_index') ? 'import_index' : null,
            ]));

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }

            $table->unsignedBigInteger('car_configuration_id')->nullable(false)->change();
        });

        if (Schema::hasIndex('car_configurations', ['car_configuration_group_id', 'import_index'], 'unique')) {
            Schema::table('car_configurations', function (Blueprint $table) {
                $table->dropUnique('car_configurations_group_id_import_index_unique');
            });
        }

        Schema::table('car_configurations', function (Blueprint $table) {
            $columnsToDrop = array_values(array_filter([
                Schema::hasColumn('car_configurations', 'import_index') ? 'import_index' : null,
                Schema::hasColumn('car_configurations', 'acceleration') ? 'acceleration' : null,
            ]));

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });

        if (Schema::hasIndex('car_configuration_groups', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_configuration_groups', function (Blueprint $table) {
                $table->dropUnique('car_configuration_groups_car_id_import_index_unique');
            });
        }

        Schema::table('car_configuration_groups', function (Blueprint $table) {
            if (Schema::hasColumn('car_configuration_groups', 'import_index')) {
                $table->dropColumn('import_index');
            }
        });

        if (Schema::hasIndex('car_reviews', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_reviews', function (Blueprint $table) {
                $table->dropUnique('car_reviews_car_id_import_index_unique');
            });
        }

        Schema::table('car_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('car_reviews', 'import_index')) {
                $table->dropColumn('import_index');
            }
        });

        if (Schema::hasIndex('car_test_drives', ['car_id', 'import_index'], 'unique')) {
            Schema::table('car_test_drives', function (Blueprint $table) {
                $table->dropUnique('car_test_drives_car_id_import_index_unique');
            });
        }

        Schema::table('car_test_drives', function (Blueprint $table) {
            if (Schema::hasColumn('car_test_drives', 'import_index')) {
                $table->dropColumn('import_index');
            }
        });

        if (Schema::hasIndex('car_crash_tests', ['car_id'], 'unique')) {
            Schema::table('car_crash_tests', function (Blueprint $table) {
                $table->dropUnique(['car_id']);
            });
        }

        Schema::table('car_crash_tests', function (Blueprint $table) {
            if (Schema::hasColumn('car_crash_tests', 'video_path')) {
                $table->dropColumn('video_path');
            }
        });

        if (Schema::hasIndex('cars', ['slug'], 'unique')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        }

        Schema::table('cars', function (Blueprint $table) {
            $table->string('year')->nullable(false)->change();
        });

        if (Schema::hasIndex('brands', ['slug'], 'unique')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        }
    }
};
