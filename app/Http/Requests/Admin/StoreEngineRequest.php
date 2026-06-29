<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\InteractsWithEnginePayload;
use Illuminate\Foundation\Http\FormRequest;

class StoreEngineRequest extends FormRequest
{
    use InteractsWithEnginePayload;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareEnginePayloadForValidation();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->engineValidationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.required' => 'Выберите бренд двигателя.',
            'brand_id.exists' => 'Выбранный бренд не найден.',
            'name.required' => 'Укажите название двигателя.',
            'name.max' => 'Название двигателя не должно превышать 255 символов.',
            'slug.required' => 'Укажите идентификатор двигателя.',
            'slug.max' => 'Идентификатор двигателя не должен превышать 255 символов.',
            'slug.unique' => 'У этого бренда уже есть двигатель с таким идентификатором.',
            'engine_url.max' => 'Ссылка на двигатель не должна превышать 255 символов.',
            'engine_type.max' => 'Тип двигателя не должен превышать 255 символов.',
            'displacement_cc.max' => 'Объем двигателя не должен превышать 255 символов.',
            'max_horsepower.max' => 'Максимальная мощность не должна превышать 255 символов.',
            'max_power_output_at_rpm.max' => 'Поле мощности при об/мин не должно превышать 255 символов.',
            'max_torque_at_rpm.max' => 'Поле крутящего момента при об/мин не должно превышать 255 символов.',
            'valves_per_cylinder.max' => 'Количество клапанов на цилиндр не должно превышать 255 символов.',
            'compression_ratio.max' => 'Степень сжатия не должна превышать 255 символов.',
            'cylinder_bore_mm.max' => 'Диаметр цилиндра не должен превышать 255 символов.',
            'piston_stroke_mm.max' => 'Ход поршня не должен превышать 255 символов.',
            'valvetrain.max' => 'Привод клапанов не должен превышать 255 символов.',
            'recommended_fuel_type.max' => 'Тип топлива не должен превышать 255 символов.',
            'fuel_consumption_l_per_100_km.max' => 'Расход топлива не должен превышать 255 символов.',
            'co2_emissions_g_per_km.max' => 'Выбросы CO2 не должны превышать 255 символов.',
            'has_start_stop_system.boolean' => 'Поле старт-стоп должно быть значением да или нет.',
        ];
    }
}
