<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_configurations', function (Blueprint $table) {
            $table->boolean('have_page')->default(true)->after('local_id');
        });
        
    }

    public function down(): void
    {
        //
    }
};
