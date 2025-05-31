<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'id_user' => 'sometimes|uuid',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'address' => 'sometimes|string',
            'id_position' => 'sometimes|nullable|uuid',
            'employment_status' => 'sometimes|in:active,inactive,resign',
        ];
    }

}
