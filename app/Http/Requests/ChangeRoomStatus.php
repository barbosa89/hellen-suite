<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRoomStatus extends FormRequest
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
            'hotel' => 'required|string|hashed_exists:hotels,id',
            'room' => 'required|string|hashed_exists:rooms,id',
            'status' => 'required|numeric|min:0|max:4|in:1,3,4'
        ];
    }
}
