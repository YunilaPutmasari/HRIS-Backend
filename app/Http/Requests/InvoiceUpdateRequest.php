<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_amount' => 'sometimes|numeric',
            'due_datetime' => 'sometimes|date',
            'status' => 'sometimes|in:paid,unpaid,failed',
        ];
    }
}
