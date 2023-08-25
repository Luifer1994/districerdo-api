<?php

namespace App\Http\Modules\Users\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
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
            'name'             => 'required|string:max:255',
            'last_name'        => 'required|string:max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'document'         => 'required|numeric:max:20',
            'email'            => 'required|email|unique:users,email,' . $this->id . ',id',
            'password'         => 'nullable|string|min:8',
            'role'             => 'required|exists:roles,name',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'name.required'                 => 'El nombre es requerido',
            'name.string'                   => 'El nombre debe ser una cadena de caracteres',
            'name.max'                      => 'El nombre debe tener máximo 255 caracteres',
            'last_name.required'            => 'El apellido es requerido',
            'last_name.string'              => 'El apellido debe ser una cadena de caracteres',
            'last_name.max'                 => 'El apellido debe tener máximo 255 caracteres',
            'document_type_id.required'     => 'El tipo de documento es requerido',
            'document_type_id.exists'       => 'El tipo de documento no existe',
            'document.required'             => 'El documento es requerido',
            'document.numeric'              => 'El documento debe ser un número',
            'document.max'                  => 'El documento debe tener máximo 20 caracteres',
            'email.required'                => 'El correo electrónico es requerido',
            'email.email'                   => 'El correo electrónico debe ser un correo válido',
            'email.unique'                  => 'El correo electrónico ya existe',
            'password.string'               => 'La contraseña debe ser una cadena de caracteres',
            'password.min'                  => 'La contraseña debe tener mínimo 8 caracteres',
            'role.required'                 => 'El rol es requerido',
            'role.exists'                   => 'El rol no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), Response::HTTP_BAD_REQUEST));
    }
}
