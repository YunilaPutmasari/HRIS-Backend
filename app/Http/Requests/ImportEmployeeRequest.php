<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employees' => 'required|array',
            'employees.*.id' => 'nullable|string',
            'employees.*.id_user' => 'nullable|string',
            'employees.*.nama' => 'required|string',
            'employees.*.jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'employees.*.phone_number' => 'nullable|string',
            'employees.*.cabang' => 'nullable|string',
            'employees.*.jabatan' => 'nullable|string',
            'employees.*.tipe_kontrak' => 'nullable|string',
            'employees.*.status' => 'nullable|in:active,inactive,resigned',
            'employees.*.Email' => 'nullable|email',
        ];
    }
}
