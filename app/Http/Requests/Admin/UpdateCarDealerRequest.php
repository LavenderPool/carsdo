<?php

namespace App\Http\Requests\Admin;

use App\Models\CarDealer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateCarDealerRequest extends StoreCarDealerRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var CarDealer $carDealer */
        $carDealer = $this->route('car_dealer');

        return [
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'dealer_id' => [
                'required',
                'integer',
                'exists:dealers,id',
                Rule::unique('car_dealers', 'dealer_id')
                    ->where(fn ($query) => $query
                        ->where('car_id', $this->input('car_id'))
                        ->where('city_id', $this->input('city_id')))
                    ->ignore($carDealer->id),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:2048'],
            'is_official' => ['boolean'],
        ];
    }
}
