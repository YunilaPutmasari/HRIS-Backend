<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'id_department' => 'required|uuid|exists:tb_department,id',
            'gaji' => 'required|numeric|min:0',
        ];
    }
}
