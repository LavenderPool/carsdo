<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('engine_url')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('displacement_cc')->nullable();
            $table->string('max_horsepower')->nullable();
            $table->string('max_power_output_at_rpm')->nullable();
            $table->string('max_torque_at_rpm')->nullable();
            $table->string('valves_per_cylinder')->nullable();
            $table->string('compression_ratio')->nullable();
            $table->string('cylinder_bore_mm')->nullable();
            $table->string('piston_stroke_mm')->nullable();
            $table->string('valvetrain')->nullable();
            $table->string('recommended_fuel_type')->nullable();
            $table->string('fuel_consumption_l_per_100_km')->nullable();
            $table->string('co2_emissions_g_per_km')->nullable();
            $table->boolean('has_start_stop_system')->nullable();
            $table->text('engine_notes')->nullable();
            $table->longText('page_text')->nullable();
            $table->timestamps();

            $table->unique(['brand_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engines');
    }
};
