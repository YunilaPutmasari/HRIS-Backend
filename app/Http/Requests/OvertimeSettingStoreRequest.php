<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OvertimeSettingStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'source' => ['required', Rule::in(['government', 'company'])],
            'rules' => 'present|array', // 'present' memastikan key 'rules' ada, meskipun kosong
            'rules.*.day_type' => ['required', Rule::in(['weekday', 'weekend', 'holiday'])],
            'rules.*.start_hour' => 'required|date_format:H:i',
            'rules.*.end_hour' => 'required|date_format:H:i|after:rules.*.start_hour',
            'rules.*.rate_multiplier' => 'required|numeric|min:0',
            'rules.*.max_hour' => 'required|integer|min:0',
            'rules.*.notes' => 'nullable|string',
        ];
    }
}
