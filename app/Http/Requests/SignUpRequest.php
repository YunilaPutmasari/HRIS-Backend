<?php

namespace App\Http\Requests;

class SignUpRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // 'address' => 'required|string',  //AKU KURANGI AGAR MEMPERSINGKAT FORM SIGN UP
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'email' => 'required|email|max:255|unique:tb_user,email',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
