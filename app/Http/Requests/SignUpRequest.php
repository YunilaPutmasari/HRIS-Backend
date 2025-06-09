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
            'company_name' => 'required|string|max:255|unique:tb_company,name',
            'company_address' => 'required|string',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
