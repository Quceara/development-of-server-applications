<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class Update2faStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password' => 'required',
            'is_2fa_enabled' => 'required|boolean',
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
            'password.required' => 'Password is required.',
            'is_2fa_enabled.required' => 'The 2FA status is required.',
            'is_2fa_enabled.boolean' => 'The 2FA status must be true or false.',
        ];
    }
}
