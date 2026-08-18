<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'room' => 'required|string|hashed_exists:rooms,id',
            'status' => [
                'required',
                'numeric',
                Rule::in([
                    Room::AVAILABLE,
                    Room::DISABLED,
                    Room::MAINTENANCE,
                ]),
            ],
        ];
    }
}
