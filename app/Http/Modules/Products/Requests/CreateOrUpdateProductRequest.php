<?php

namespace App\Http\Modules\Products\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrUpdateProductRequest extends FormRequest
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
            'name'                  => 'required|string:max:100',
            'description'           => 'nullable|string:max:255',
            'category_id'           => 'required|integer|exists:categories,id',
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
            'name.required'                 => 'El nombre es requerido',
            'name.string'                   => 'El nombre debe ser una cadena de caracteres',
            'name.max'                      => 'El nombre debe tener máximo 100 caracteres',
            'description.string'            => 'La descripción debe ser una cadena de caracteres',
            'description.max'               => 'La descripción debe tener máximo 255 caracteres',
            'category_id.required'          => 'La categoría es requerida',
            'category_id.integer'           => 'La categoría debe ser un número entero',
            'category_id.exists'            => 'La categoría no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
