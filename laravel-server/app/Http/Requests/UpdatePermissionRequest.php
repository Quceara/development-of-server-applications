<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|unique:permissions,name,' . $this->route('id'),
            'slug' => 'required|unique:permissions,slug,' . $this->route('id'),
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
