<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignAsset extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset' => 'required|integer|exists:assets,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'asset' => id_decode($this->asset),
        ]);
    }
}
