<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropsTransaction extends FormRequest
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
            'props.*.hash' => 'required|string|hashed_exists:props,id',
            'props.*.amount' => 'required|numeric|min:1',
            'props.*.commentary' => 'required|string|max:255',
            'type' => 'required|string|in:input,output'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    // public function messages()
    // {
    //     return [
    //         'room.*.price.price' => 'Hay un error en el precio',
    //         'room.*.start.after_or_equal' => 'La fecha inicial debe ser igual o superior al día actual',
    //         'room.*.end.after' => 'La fecha final debe ser superior a la fecha inicial',
    //     ];
    // }
}
