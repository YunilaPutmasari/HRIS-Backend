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
            'avatar' => 'sometimes|nullable|file|image|mimes:jpeg,png,jpg,gif|max:5000', // max 2MB
            'id_user' => 'sometimes|uuid',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'nik' => 'sometimes|nullable|string|max:100',
            'address' => 'sometimes|nullable|string|max:500',
            'tempat_lahir' => 'sometimes|nullable|string|max:255',
            'tanggal_lahir' => 'sometimes|nullable|date|before:today',
            'jenis_kelamin' => 'sometimes|string|in:Laki-laki,Perempuan',
            'pendidikan' => 'sometimes|string|in:SMA/SMK,D3,S1,S2,S3',
            'email' => 'sometimes|email|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:startDate',
            'tenure' => 'sometimes|string|max:50',
            'jadwal' => 'sometimes|string|in:Shift,Non-Shift',
            'tipe_kontrak' => 'sometimes|string|in:Kontrak,Tetap,Magang',
            'cabang' => 'sometimes|string|max:255',
            'employment_status' => 'sometimes|string|in:active,inactive,resign',
            'id_position' => 'sometimes|string|max:255',
            'id_department' => 'sometimes|string|max:255',
            'jabatan' => 'sometimes|string|max:255',
            'tanggal_efektif' => 'sometimes|date',
            'bank' => 'sometimes|string|max:50',
            'norek' => 'sometimes|string|max:50',
            'gaji' => 'sometimes|numeric|min:0',
            'uang_lembur' => 'sometimes|numeric|min:0',
            'denda_terlambat' => 'sometimes|numeric|min:0',
            'total_gaji' => 'sometimes|numeric|min:0',
            'dokumen' => 'sometimes|array',
            'dokumen.*' => 'file|mimes:pdf,doc,docx|max:2048',
            'documents.*.id' => 'sometimes|uuid',
            'documents.*.title' => 'sometimes|string|max:255',
            'documents.*.fileUrl' => 'sometimes|url',
            'documents.*.uploadDate' => 'sometimes|date',
        ];

    }

}
