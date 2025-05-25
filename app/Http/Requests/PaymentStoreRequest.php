<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_invoice' => 'required|uuid|exists:tb_invoice,id',
            'payment_code' => 'required|string',
            'amount_paid' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|in:credit_card,bank_transfer,e_wallet',
            'payment_datetime' => 'required|date',
        ];
    }
}
