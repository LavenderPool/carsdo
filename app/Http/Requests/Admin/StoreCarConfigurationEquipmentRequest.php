<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarConfigurationEquipmentRequest extends FormRequest
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
            'car_configuration_equipment_category_id' => ['required', 'integer', 'exists:car_configuration_equipment_categories,id'],
            'import_index' => ['nullable', 'integer', 'min:0'],
            'value' => ['nullable', 'string'],
            'is_extension' => ['boolean'],
            'price' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
