<?php

namespace App\Http\Requests;

class SignUpRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tb_user,email',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:8',
            'address' => 'required|string|max:255',
        ];
    }
}
