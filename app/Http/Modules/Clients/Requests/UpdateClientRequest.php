<?php

namespace App\Http\Modules\Clients\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'              => 'required|string',
            'last_name'         => 'required|string',
            'email'             => 'required|email|unique:clients,email,' . $this->id . ',id',
            'phone'             => 'nullable|string',
            'document_number'   => 'required|string',
            'address'           => 'required|string',
            'document_type_id'  => 'required|integer|exists:document_types,id',
            'city_id'           => 'required|integer|exists:cities,id',
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
            'name.required'             => 'El nombre es requerido',
            'name.string'               => 'El nombre debe ser un texto',
            'last_name.required'        => 'El apellido es requerido',
            'last_name.string'          => 'El apellido debe ser un texto',
            'email.required'            => 'El correo electrónico es requerido',
            'email.email'               => 'El correo electrónico debe ser un correo electrónico válido',
            'email.unique'              => 'El correo electrónico ya está en uso',
            'phone.string'              => 'El teléfono debe ser un texto',
            'document_number.required'  => 'El número de documento es requerido',
            'document_number.string'    => 'El número de documento debe ser un texto',
            'address.required'          => 'La dirección es requerida',
            'address.string'            => 'La dirección debe ser un texto',
            'document_type_id.required' => 'El tipo de documento es requerido',
            'document_type_id.integer'  => 'El tipo de documento debe ser un número entero',
            'document_type_id.exists'   => 'El tipo de documento no existe',
            'city_id.required'          => 'La ciudad es requerida',
            'city_id.integer'           => 'La ciudad debe ser un número entero',
            'city_id.exists'            => 'La ciudad no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
