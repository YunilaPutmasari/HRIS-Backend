<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_code' => 'sometimes|string',
            'amount_paid' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'payment_method' => 'sometimes|in:credit_card,bank_transfer,e_wallet',
            'payment_datetime' => 'sometimes|date',
            'status' => 'sometimes|in:success,failed',
        ];
    }
}
