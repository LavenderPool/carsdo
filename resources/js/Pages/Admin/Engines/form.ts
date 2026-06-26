export type EngineFormData = {
    brand_id: number | null;
    name: string;
    slug: string;
    engine_url: string;
    engine_type: string;
    displacement_cc: string;
    max_horsepower: string;
    max_power_output_at_rpm: string;
    max_torque_at_rpm: string;
    valves_per_cylinder: string;
    compression_ratio: string;
    cylinder_bore_mm: string;
    piston_stroke_mm: string;
    valvetrain: string;
    recommended_fuel_type: string;
    fuel_consumption_l_per_100_km: string;
    co2_emissions_g_per_km: string;
    has_start_stop_system: '' | '0' | '1';
    engine_notes: string;
    page_text: string;
};

export type EngineFormSource = Partial<Omit<EngineFormData, 'has_start_stop_system'>> & {
    has_start_stop_system?: boolean | null;
};

export const createEngineFormData = (engine: EngineFormSource = {}): EngineFormData => ({
    brand_id: engine.brand_id ?? null,
    name: engine.name ?? '',
    slug: engine.slug ?? '',
    engine_url: engine.engine_url ?? '',
    engine_type: engine.engine_type ?? '',
    displacement_cc: engine.displacement_cc ?? '',
    max_horsepower: engine.max_horsepower ?? '',
    max_power_output_at_rpm: engine.max_power_output_at_rpm ?? '',
    max_torque_at_rpm: engine.max_torque_at_rpm ?? '',
    valves_per_cylinder: engine.valves_per_cylinder ?? '',
    compression_ratio: engine.compression_ratio ?? '',
    cylinder_bore_mm: engine.cylinder_bore_mm ?? '',
    piston_stroke_mm: engine.piston_stroke_mm ?? '',
    valvetrain: engine.valvetrain ?? '',
    recommended_fuel_type: engine.recommended_fuel_type ?? '',
    fuel_consumption_l_per_100_km: engine.fuel_consumption_l_per_100_km ?? '',
    co2_emissions_g_per_km: engine.co2_emissions_g_per_km ?? '',
    has_start_stop_system: engine.has_start_stop_system === null || engine.has_start_stop_system === undefined
        ? ''
        : engine.has_start_stop_system
          ? '1'
          : '0',
    engine_notes: engine.engine_notes ?? '',
    page_text: engine.page_text ?? '',
});
