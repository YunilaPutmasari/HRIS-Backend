<?php

namespace App\Http\Requests;

class SignInRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            "email" => "email|max:255|exists:tb_user,email",
            "phone_number" => "string|max:15",
            "password" => "required|min:8",
        ];
    }
}
