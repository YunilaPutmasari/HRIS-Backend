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
            "id_user" => "required|integer|exists:tb_user,id",
            "request_type" => "required|string|max:255",
            "start_date" => "required|date_format:Y-m-d H:i:s",
            "end_date" => "required|date_format:Y-m-d H:i:s",
            "reason" => "required|string|max:255",
            "status" => "required|string|max:255",
            "approved_by" => "nullable|integer|exists:tb_user,id",
        ];
    }
}
