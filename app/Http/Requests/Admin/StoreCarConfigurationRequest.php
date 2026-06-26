<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_configuration_group_id' => ['required', 'integer', 'exists:car_configuration_groups,id'],
            'import_index' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'integer', 'min:0'],
            'engine_id' => ['nullable', 'integer', 'exists:engines,id'],
            'engine_type' => ['nullable', 'string', 'max:255'],
            'engine_capacity' => ['nullable', 'numeric', 'min:0'],
            'horsepower' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'max:255'],
            'drive_type' => ['nullable', 'string', 'max:255'],
            'fuel_city' => ['nullable', 'numeric', 'min:0'],
            'fuel_highway' => ['nullable', 'numeric', 'min:0'],
            'fuel_combined' => ['nullable', 'numeric', 'min:0'],
            'acceleration' => ['nullable', 'numeric', 'min:0'],
            'speed' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
