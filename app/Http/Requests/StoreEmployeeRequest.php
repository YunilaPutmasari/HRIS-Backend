<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'id_user' => 'nullable|uuid',
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'nik' => 'required|string',
            'email' => 'required|email|unique:tb_user,email',
            'address' => 'required|string',
            'jenisKelamin' => 'nullable|string',
            'notelp' => 'nullable|string',
            'cabang' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'grade' => 'nullable|string',
            'bank' => 'nullable|string',
            'norek' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'jadwal' => 'nullable|string',
            'tipeKontrak' => 'nullable|string',
            'tempatLahir' => 'nullable|string',
            'tanggalLahir' => 'nullable|date',
            'dokumen' => 'required|file|mimes:pdf,doc,docx|max:2048', // ✅ benar

            'status' => 'in:active,inactive,resign',

        ];
    }
}
