<?php

namespace App\Http\Requests;

use App\Helpers\Id;
use Illuminate\Foundation\Http\FormRequest;

class UpdateService extends FormRequest
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
        $id = Id::get($this->route('service'));
        
        return [
            'description' => 'required|string|max:191|unique:services,description, ' . $id,
            'price' => 'required|numeric|min:1',
        ];
    }
}
