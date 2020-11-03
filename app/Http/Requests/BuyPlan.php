<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyPlan extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'plan_id' => 'required|integer|exists:plans,id',
            'type_id' => 'required|integer|exists:identification_types,id',
            'customer_dni' => 'required|numeric|digits_between:5,20|min:1',
            'customer_name' => 'required|string|min:3|max:120',
            'currency_id' => 'required|integer|exists:currencies,id'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'plan_id' => id_decode($this->plan_id),
            'type_id' => id_decode($this->type_id),
            'currency_id' => id_decode($this->currency_id)
        ]);
    }
}
