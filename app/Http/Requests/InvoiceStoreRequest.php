<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\InvoiceStatus;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_subscription'=>'required|exists:tb_subscription,id',
            'total_amount' => 'required|numeric',
            'due_datetime' => 'required|date',
        ];
    }
}
