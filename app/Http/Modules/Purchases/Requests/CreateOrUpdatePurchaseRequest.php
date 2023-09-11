<?php

namespace App\Http\Modules\Purchases\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrUpdatePurchaseRequest extends FormRequest
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
        $rules = [
            'provider_id' => 'required|integer|exists:providers,id',
            'status' => 'required|string|in:PENDING,PAID',
            'purchase_lines' => 'required|array',
            'purchase_lines.*.product_id' => 'required|integer|exists:products,id',
            'purchase_lines.*.price' => 'required|numeric',
            'purchase_lines.*.quantity' => 'required|numeric'
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['code'] = 'required|string|max:8|unique:invoices,code,' . $this->route('invoice')->id;
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'provider_id.required' => 'El proveedor es requerido',
            'provider_id.integer' => 'El proveedor debe ser un entero',
            'provider_id.exists' => 'El proveedor no existe',
            'status.required' => 'El estado es requerido',
            'status.string' => 'El estado debe ser un string',
            'status.in' => 'El estado debe ser PENDING o PAID',
            'purchase_lines.required' => 'Las líneas de compra son requeridas',
            'purchase_lines.array' => 'Las líneas de compra deben ser un arreglo',
            'purchase_lines.*.product_id.required' => 'El producto es requerido',
            'purchase_lines.*.product_id.integer' => 'El producto debe ser un entero',
            'purchase_lines.*.product_id.exists' => 'El producto no existe',
            'purchase_lines.*.price.required' => 'El precio es requerido',
            'purchase_lines.*.price.numeric' => 'El precio debe ser un número',
            'purchase_lines.*.quantity.required' => 'La cantidad es requerida',
            'purchase_lines.*.quantity.numeric' => 'La cantidad debe ser un número'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
