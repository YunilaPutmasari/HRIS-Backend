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
            // 'email' => 'required|string',
            'address' => 'required|string',
            'jenisKelamin' => 'required|string',
            'notelp' => 'required|string',
            'cabang' => 'nullable|string',
            'id_position' => 'required|exists:tb_position,id',
            'grade' => 'nullable|string',
            'bank' => 'required|string',
            'norek' => 'required|string',
            'pendidikan' => 'required|string',
            'jadwal' => 'required|string',
            'tipeKontrak' => 'required|string',
            'tempatLahir' => 'required|string',
            'tanggalLahir' => 'required|date',
            'tanggalEfektif' => 'nullable|date',
            'dokumen' => 'nullable|array', // dokumen harus berupa array jika ada
            'dokumen.*' => 'file|mimes:pdf,doc,docx|max:2048', // setiap elemen array harus file valid

            'employment_status' => 'in:active,inactive,resign',
            'gaji' => 'required|numeric|min:0',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:start_date',
            'tenure' => 'required|string',
            'uangLembur' => 'required|numeric|min:0',
            'dendaTerlambat' => 'required|numeric|min:0',
            'TotalGaji' => 'required|numeric|min:0',


        ];
    }
}
