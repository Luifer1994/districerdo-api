<?php

namespace App\Http\Modules\RolesAndPermissions\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class AsingPermissionsToRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'role_id'          => 'required|exists:roles,id',
            'permissions'      => 'required|array',
            'permissions.*'    => 'required|exists:permissions,id',
            'group'            => 'required|exists:permissions,group',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'role_id.required'          => 'El campo rol es obligatorio',
            'role_id.exists'            => 'El rol no existe',
            'permissions.required'      => 'El campo permisos es obligatorio',
            'permissions.array'         => 'El campo permisos debe ser un array',
            'permissions.*.required'    => 'El campo permisos es obligatorio',
            'permissions.*.exists'      => 'El permiso no existe',
            'group.required'            => 'El campo grupo es obligatorio',
            'group.exists'              => 'El grupo no existe'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
