<?php

namespace App\Http\Modules\Products\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateStockRequest extends FormRequest
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
            'product_id'            => 'required|integer|exists:products,id',
            'quantity'              => 'required|numeric|min:1',
            'batch'                 => 'required|string|exists:batches,code',
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
            'product_id.required'       => 'El producto es requerido',
            'product_id.integer'        => 'El producto debe ser un número entero',
            'product_id.exists'         => 'El producto no existe',
            'quantity.required'         => 'La cantidad es requerida',
            'quantity.numeric'          => 'La cantidad debe ser un número',
            'quantity.min'              => 'La cantidad debe ser mayor a 0',
            'batch.required'            => 'El lote es requerido',
            'batch.string'              => 'El lote debe ser una cadena de caracteres',
            'batch.exists'              => 'El lote no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
