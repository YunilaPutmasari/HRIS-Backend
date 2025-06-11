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
            // 'id_user' => 'required|uuid',
            'email' => 'required|email|unique:tb_user,email',
            'password' => 'required|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            // Optional fields
            'nik' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'pendidikan' => 'nullable|string|in:SMA/SMK,D3,S1,S2,S3',
            'no_telp' => 'nullable|string|max:20',
            'id_position' => 'nullable|uuid',
            'tipe_kontrak' => 'nullable|string|in:Tetap,Kontrak,Magang',
            'cabang' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|in:active,inactive,resign',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'tanggal_efektif' => 'nullable|date',
            'bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:255',
            'jadwal' => 'nullable|string|in:Shift,Non-Shift',

            // File uploads
            'avatar' => 'nullable|image|max:2048', // 2MB max
            'dokumen.*' => 'nullable|file|mimes:pdf,docx|max:5000', // 5MB max
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'id_workplace.required' => 'Workplace/Company is required',
            'id_workplace.exists' => 'Selected workplace/company is invalid',
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'id_position.exists' => 'Selected position is invalid',
            'id_department.exists' => 'Selected department is invalid',
        ];
    }
}
