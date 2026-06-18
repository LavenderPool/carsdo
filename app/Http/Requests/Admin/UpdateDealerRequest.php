<?php

namespace App\Http\Requests\Admin;

use App\Models\Dealer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateDealerRequest extends StoreDealerRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Dealer $dealer */
        $dealer = $this->route('dealer');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dealers', 'name')->ignore($dealer->id),
            ],
        ];
    }
}
