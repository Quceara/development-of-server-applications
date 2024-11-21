<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|unique:permissions,name',
            'slug' => 'required|unique:permissions,slug',
            'description' => 'nullable',
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
            'name.required' => 'The permission name is required.',
            'name.unique' => 'The permission name has already been taken.',
            'slug.required' => 'The permission slug is required.',
            'slug.unique' => 'The permission slug has already been taken.',
        ];
    }
}
