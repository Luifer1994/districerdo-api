<?php

namespace App\Http\Modules\Invoices\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateOrUpdateInvoiceRequest extends FormRequest
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
            'state'                                                 => 'required|in:CANCELLED,PAID,PENDING',
            'observation'                                           => 'nullable|string',
            'client_id'                                             => 'required|exists:clients,id',
            'invoice_lines'                                         => 'required|array',
            'invoice_lines.*.price'                                 => 'required|numeric',
            'invoice_lines.*.quantity'                              => 'required|integer|min:1',
            'invoice_lines.*.product_id'                            => 'required|exists:products,id',
            'invoice_lines.*.batch'                              => 'required|exists:batches,code',
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
            'state.required'                                        => 'El estado es requerido',
            'state.in'                                              => 'El estado debe ser CANCELLED, PAID o PENDING',
            'observation.string'                                    => 'La observación debe ser una cadena de texto',
            'client_id.required'                                    => 'El cliente es requerido',
            'client_id.exists'                                      => 'El cliente no existe',
            'invoice_lines.required'                                => 'Las líneas de factura son requeridas',
            'invoice_lines.array'                                   => 'Las líneas de factura deben ser un arreglo',
            'invoice_lines.*.price.required'                        => 'El precio es requerido',
            'invoice_lines.*.price.numeric'                         => 'El precio debe ser un número',
            'invoice_lines.*.quantity.required'                     => 'La cantidad es requerida',
            'invoice_lines.*.quantity.integer'                      => 'La cantidad debe ser un número entero',
            'invoice_lines.*.quantity.min'                          => 'La cantidad debe ser mayor a 0',
            'invoice_lines.*.product_id.required'                   => 'El producto es requerido',
            'invoice_lines.*.product_id.exists'                     => 'El producto no existe',
            'invoice_lines.*.batch.required'                        => 'El lote es requerido',
            'invoice_lines.*.batch.exists'                          => 'El lote no existe',
            'code.required'                                         => 'El código es requerido',
            'code.string'                                           => 'El código debe ser una cadena de texto',
            'code.max'                                              => 'El código debe tener máximo 8 caracteres',
            'code.unique'                                           => 'El código ya existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
