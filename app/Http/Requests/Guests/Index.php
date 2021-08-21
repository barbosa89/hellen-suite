<?php

namespace App\Http\Requests\Guests;

use App\Models\Guest;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
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
            'from_date' => ['bail', 'nullable', 'date', 'before_or_equal:today'],
            'status' => ['bail', 'nullable', 'string', Rule::in(Guest::SCOPE_STATUS)],
            'search' => ['bail', 'nullable', 'alpha_num', 'min:3', 'max:30'],
        ];
    }
}
