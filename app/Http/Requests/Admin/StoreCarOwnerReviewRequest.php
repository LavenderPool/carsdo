<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarOwnerReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'import_index' => ['nullable', 'integer', 'min:0'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'full_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:10240'],
            'text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Укажите рейтинг.',
            'rating.between' => 'Рейтинг должен быть от 1 до 5.',
            'full_name.required' => 'Укажите ФИО.',
            'text.required' => 'Введите текст отзыва.',
            'photo.image' => 'Фото должно быть изображением.',
        ];
    }
}
