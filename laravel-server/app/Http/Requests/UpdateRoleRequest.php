<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'nullable|unique:roles,name,' . $this->route('id'),
            'slug' => 'nullable|unique:roles,slug,' . $this->route('id'),
            'description' => 'nullable',
            'permission_ids' => 'nullable|array|exists:permissions,id',
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
            'name.unique' => 'The role name must be unique.',
            'slug.unique' => 'The role slug must be unique.',
            'permission_ids.array' => 'The permissions must be an array.',
            'permission_ids.exists' => 'Some of the selected permissions are invalid.',
        ];
    }
}
