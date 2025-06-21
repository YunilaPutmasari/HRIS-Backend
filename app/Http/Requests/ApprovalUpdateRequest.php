<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "request_type" => "required|string|max:255",
            "start_date" => "required|date_format:Y-m-d H:i",
            "end_date" => "required|date_format:Y-m-d H:i",
            "reason" => "required|string|max:255",
        ];
    }
}
