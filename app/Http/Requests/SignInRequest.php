<?php

namespace App\Http\Requests;

class SignInRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "email" => "nullable|email|max:255|exists:tb_user,email",
            "id_employee" => "nullable|string|exists:tb_employee,id",
            "company_name" => "nullable|string|exists:tb_employee,company_name",
            "phone_number" => "nullable|string|max:15|exists:tb_user,phone_number",
            "password" => "required|min:8",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->email && !$this->phone_number && !$this->id_employee && !$this->company_name) {
                $validator->errors()->add('email', 'Either email or phone number or employee ID is required.');
                $validator->errors()->add('phone_number', 'Either email or phone number or employee ID is required.');
                $validator->errors()->add('id_employee', 'Either email or phone number or employee ID is required.');
            }
        });
    }
}
