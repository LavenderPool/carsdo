<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateCarPhotoRequest extends StoreCarPhotoRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_photo_group_id' => ['required', 'integer', 'exists:car_photo_groups,id'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ];
    }
}
