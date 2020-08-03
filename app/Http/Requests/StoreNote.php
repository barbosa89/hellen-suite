<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Purifier\Facades\Purifier;

class StoreNote extends FormRequest
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
            'hotel_id' => 'required|integer|exists:hotels,id|open_shift',
            'content' => 'required|string|max:2400',
            'tags.*' => 'required|integer|exists:tags,id',
            'add' => 'required|boolean'
        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $data = $this->all();

        $data['content'] = Purifier::clean($data['content']);
        $data['hotel_id'] = id_decode($data['hotel_id']);
        $data['tags'] = id_decode_recursive(collect($data['tags'])->pluck('hash')->toArray());

        return $data;
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        return array_merge(parent::validated(), [
            'team_member_name' => auth()->user()->name,
            'team_member_email' => auth()->user()->email
        ]);
    }
}
