<?php

namespace App\Http\Modules\Invoices\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'state'                                                 => 'required|in:draft,canceled,paid,pending',
            'observation'                                           => 'nullable|string',
            'client_id'                                             => 'required|exists:clients,id',
            'invoice_lines'                                         => 'required|array',
            'invoice_lines.*.price'                                 => 'required|numeric',
            'invoice_lines.*.quantity'                              => 'required|integer|min:1',
            'invoice_lines.*.service_id'                            => 'required|exists:services,id',
            'invoice_lines.*.invoice_line_supplies'                 => 'nullable|array',
            'invoice_lines.*.invoice_line_supplies.*.description'   => 'required|string',
            'invoice_lines.*.invoice_line_supplies.*.price'         => 'required|numeric',
            'invoice_lines.*.invoice_line_supplies.*.quantity'      => 'required|integer|min:1',
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
            'state.required'                                               => 'El estado es obligatorio.',
            'state.in'                                                     => 'El estado debe ser "borrador", "cancelado", "pagado" o "pendiente".',
            'observation.string'                                           => 'La observación debe ser una cadena de texto.',
            'client_id.required'                                           => 'El cliente es obligatorio.',
            'client_id.exists'                                             => 'El cliente especificado no existe.',
            'invoice_lines.required'                                       => 'Las líneas de factura son obligatorias.',
            'invoice_lines.array'                                          => 'Las líneas de factura deben ser un arreglo.',
            'invoice_lines.*.price.required'                               => 'El precio de la línea de factura es obligatorio.',
            'invoice_lines.*.price.numeric'                                => 'El precio de la línea de factura debe ser numérico.',
            'invoice_lines.*.quantity.required'                            => 'La cantidad de la línea de factura es obligatoria.',
            'invoice_lines.*.quantity.integer'                             => 'La cantidad de la línea de factura debe ser un número entero.',
            'invoice_lines.*.quantity.min'                                 => 'La cantidad de la línea de factura debe ser al menos 1.',
            'invoice_lines.*.service_id.required'                          => 'El servicio de la línea de factura es obligatorio.',
            'invoice_lines.*.service_id.exists'                            => 'El servicio de la línea de factura especificado no existe.',
            'invoice_lines.*.invoice_line_supplies.array'                  => 'Los suministros de la línea de factura deben ser un arreglo.',
            'invoice_lines.*.invoice_line_supplies.*.description.required' => 'La descripción del suministro de la línea de factura es obligatoria.',
            'invoice_lines.*.invoice_line_supplies.*.description.string'   => 'La descripción del suministro de la línea de factura debe ser una cadena de texto.',
            'invoice_lines.*.invoice_line_supplies.*.price.required'       => 'El precio del suministro de la línea de factura es obligatorio.',
            'invoice_lines.*.invoice_line_supplies.*.price.numeric'        => 'El precio del suministro de la línea de factura debe ser numérico.',
            'invoice_lines.*.invoice_line_supplies.*.quantity.required'    => 'La cantidad del suministro de la línea de factura es obligatoria.',
            'invoice_lines.*.invoice_line_supplies.*.quantity.integer'     => 'La cantidad del suministro de la línea de factura debe ser un número entero.',
            'invoice_lines.*.invoice_line_supplies.*.quantity.min'         => 'La cantidad del suministro de la línea de factura debe ser al menos 1.',
            'code.required'                                                => 'El código de la factura es obligatorio.',
            'code.string'                                                  => 'El código de la factura debe ser una cadena de texto.',
            'code.max'                                                     => 'El código de la factura no debe exceder los 8 caracteres.',
            'code.unique'                                                  => 'El código de la factura ya está en uso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
