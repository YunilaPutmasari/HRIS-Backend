<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->first_name . ' ' . $this->last_name,
            'address' => $this->address,
            'nik' => $this->nik ?? '',
            'email' => $this->user?->email ?? '',
            'jenisKelamin' => $this->jenisKelamin ?? '',
            'notelp' => $this->notelp ?? '',
            'cabang' => $this->cabang ?? '',
            'jabatan' => $this->jabatan ?? '',
            'grade' => $this->grade ?? '',
            'bank' => $this->bank ?? '',
            'norek' => $this->norek ?? '',
            'pendidikan' => $this->pendidikan ?? '',
            'jadwal' => $this->jadwal ?? '',
            'tipeKontrak' => $this->tipeKontrak ?? '',
            'tempatLahir' => $this->tempatLahir ?? '',
            'tanggalLahir' => $this->tanggalLahir ?? '',
            'status' => $this->employment_status,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            // 'dokumen' => $this->dokumen ? asset('storage/' . $this->dokumen) : null,
            'dokumen' => $this->documents?->map(function ($dok) {
                return [
                    'id' => $dok->id,
                    'name' => $dok->name,
                    'file' => $dok->file_path ? asset('storage/' . $dok->file_path) : null,

                ];
            }) ?? [],
            'gajiPokok' => $this->payroll?->total_salary ? 'Rp ' . number_format($this->payroll->total_salary, 0, ',', '.') : 'Rp 0',
            'uangLembur' => $this->payroll?->overtime_wage ? 'Rp ' . number_format($this->payroll->overtime_wage, 0, ',', '.') : 'Rp 0',
            'total' => $this->payroll?->total_wage ? 'Rp ' . number_format($this->payroll->total_wage, 0, ',', '.') : 'Rp 0',




        ];
    }


    private function position_name()
    {
        // Cukup cek kalau relasi position sudah ada
        return $this->position ? $this->position->name : null;
    }
}
