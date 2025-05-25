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
            'id_user' => 'required|uuid|exists:tb_user,id',
            'total_amount' => 'required|numeric',
            'due_datetime' => 'required|date',
            'status' => 'required|in:' . implode(',', InvoiceStatus::all()),
        ];
    }
}
