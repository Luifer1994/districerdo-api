<?php

namespace App\Http\Modules\Purchases\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentPartialPurchaseRequest extends FormRequest
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
            'purchase_id' => 'required|integer|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
