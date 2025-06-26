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
        return true;
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
            "request_type" => "required|in:overtime,permit,leave,sick",
            "start_date" => "required|date_format:Y-m-d H:i",
            "end_date" => "required|date_format:Y-m-d H:i|after_or_equal:start_date",
            "reason" => "required|string|max:255",
            "document" => "nullable|file|mimes:pdf,jpg,jpeg,png", // Maksimal 2MB
        ];
    }

    public function messages() {
        return [
            'end_date.after_or_equal' => "End date must be after start date",
        ];
    }
}
