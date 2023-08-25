<?php

namespace App\Http\Modules\Users\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ChangePasswordRequest extends FormRequest
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
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|min:8|same:new_password',
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
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser un texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'new_password.required' => 'La nueva contraseña es requerida',
            'new_password.string' => 'La nueva contraseña debe ser un texto',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'new_password_confirmation.required' => 'La confirmación de la nueva contraseña es requerida',
            'new_password_confirmation.string' => 'La confirmación de la nueva contraseña debe ser un texto',
            'new_password_confirmation.min' => 'La confirmación de la nueva contraseña debe tener al menos 8 caracteres',
            'new_password_confirmation.same' => 'La confirmación de la nueva contraseña no coincide',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), Response::HTTP_BAD_REQUEST));
    }
}
