<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTeamMember extends FormRequest
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
            'hotel' => 'required|string|max:50|hashed_exists:hotels,id'
        ];
    }
}
