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
            'id_user' => 'required|uuid',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5000',
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'nullable|string',
            'nik' => 'required|string',
            'email' => 'required|string',
            'address' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'no_telp' => 'required|string',
            'cabang' => 'nullable|string',
            'id_position' => 'required|exists:tb_position,id',
            'grade' => 'nullable|string',
            'bank' => 'required|string',
            'norek' => 'required|string',
            'pendidikan' => 'required|string',
            'jadwal' => 'required|string',
            'tipe_kontrak' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'tanggal_efektif' => 'nullable|date',
            'dokumen' => 'nullable|array', // dokumen harus berupa array jika ada
            'dokumen.*' => 'file|mimes:pdf,doc,docx|max:2048', // setiap elemen array harus file valid

            'employment_status' => 'in:active,inactive,resign',
            'gaji' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'tenure' => 'required|string',
            'uang_lembur' => 'required|numeric|min:0',
            'denda_terlambat' => 'required|numeric|min:0',
            'total_gaji' => 'required|numeric|min:0',


        ];
    }
}
