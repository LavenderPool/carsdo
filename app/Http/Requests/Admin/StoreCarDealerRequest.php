<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarDealerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'address' => $this->normalizeNullableString('address'),
            'phone' => $this->normalizeNullableString('phone'),
            'website' => $this->normalizeNullableString('website'),
            'is_official' => $this->boolean('is_official'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'dealer_id' => [
                'required',
                'integer',
                'exists:dealers,id',
                Rule::unique('car_dealers', 'dealer_id')->where(fn ($query) => $query
                    ->where('car_id', $this->input('car_id'))
                    ->where('city_id', $this->input('city_id'))),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:2048'],
            'is_official' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'car_id.required' => 'Выберите автомобиль.',
            'car_id.exists' => 'Выбранный автомобиль не найден.',
            'dealer_id.required' => 'Выберите дилера.',
            'dealer_id.exists' => 'Выбранный дилер не найден.',
            'dealer_id.unique' => 'Такая связка автомобиль, дилер и город уже существует.',
            'city_id.required' => 'Выберите город.',
            'city_id.exists' => 'Выбранный город не найден.',
            'website.url' => 'Сайт дилера должен быть корректной ссылкой.',
        ];
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = $this->input($key);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
