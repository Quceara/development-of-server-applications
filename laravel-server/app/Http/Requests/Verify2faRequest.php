<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class Verify2faRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => 'required|digits:6',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'code.required' => 'Access code is required.',
            'code.digits' => 'Access code must be exactly 6 digits.',
        ];
    }
}
