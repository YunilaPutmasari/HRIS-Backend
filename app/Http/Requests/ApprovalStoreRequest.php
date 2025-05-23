<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id_user" => "required|exists:tb_user,id",
            "request_type" => "required|in:cuti,izin,sakit",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
            "reason" => "required|string|max:255",
            "status" => "required|in:pending,approved,rejected",
            "approved_by" => "nullable|exists:tb_user,id",
        ];
    }
}
