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
            'employee.*.id_user' => 'nullable|uuid',
            'employee.*.avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'employee.*.first_name' => 'nullable|string|max:255',
            'employee.*.last_name' => 'nullable|string',
            'employee.*.nik' => 'nullable|string',
            'employee.*.email' => 'nullable|email|unique:tb_user,email',
            'employee.*.address' => 'nullable|string',
            'employee.*.jenisKelamin' => 'nullable|string',
            'employee.*.notelp' => 'nullable|string',
            'employee.*.cabang' => 'nullable|string',
            'employee.*.id_position' => 'nullable|exists:tb_position,id',
            'employee.*.grade' => 'nullable|string',
            'employee.*.bank' => 'nullable|string',
            'employee.*.norek' => 'nullable|string',
            'employee.*.pendidikan' => 'nullable|string',
            'employee.*.jadwal' => 'nullable|string',
            'employee.*.tipeKontrak' => 'nullable|string',
            'employee.*.tempatLahir' => 'nullable|string',
            'employee.*.tanggalLahir' => 'nullable|date',
            'employee.*.tanggalEfektif' => 'nullable|date',
            'employee.*.dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'employee.*.employment_status' => 'nullable|in:active,inactive,resign',
            'employee.*.gaji' => 'nullable|numeric|min:0',
            'employee.*.startDate' => 'nullable|date',
            'employee.*.endDate' => 'nullable|date|after_or_equal:employee.*.startDate',
            'employee.*.tenure' => 'nullable|string',
            'employee.*.uangLembur' => 'nullable|numeric|min:0',
            'employee.*.dendaTerlambat' => 'nullable|numeric|min:0',
            'employee.*.TotalGaji' => 'nullable|numeric|min:0',
        ];
    }
}
