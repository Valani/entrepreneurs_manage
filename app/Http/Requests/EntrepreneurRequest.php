<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntrepreneurRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'ipn' => ['required', 'string', Rule::unique('entrepreneurs')->ignore($this->entrepreneur)],
            'iban' => 'required|string|max:34',
            'tax_office_name' => 'required|string|max:255',
            'group' => 'required|in:1,2,3',
            'kveds' => 'array',
            'kveds.*' => 'exists:kveds,id_kved'
        ];

        return $rules;
    }
}