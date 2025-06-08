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
            'id_user' => $this->id_user,
            'nama' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nik' => $this->nik ?? '',
            'address' => $this->address,
            'tempatLahir' => $this->tempatLahir ?? '',
            'tanggalLahir' => $this->tanggalLahir ?? '',
            'jenisKelamin' => $this->jenisKelamin ?? '',
            'pendidikan' => $this->pendidikan ?? '',
            'email' => $this->user?->email ?? '',
            'notelp' => $this->notelp ?? '',
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'tenure' => $this->tenure,
            'jadwal' => $this->jadwal ?? '',
            'tipeKontrak' => $this->tipeKontrak ?? '',
            'cabang' => $this->cabang ?? '',
            'employment_status' => $this->employment_status ?? '',
            'jabatan' => $this->position ? $this->position->name : null,
            'id_position' => $this->id_position,
            'tanggalEfektif' => $this->tanggalEfektif,
            'bank' => $this->bank ?? '',
            'norek' => $this->norek ?? '',
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            // 'dokumen' => $this->dokumen ? asset('storage/' . $this->dokumen) : null,
            'dokumen' => $this->documents?->map(function ($dok) {
                return [
                    'id' => $dok->id,
                    'name' => $dok->name,
                    'file' => $dok->file_path ? asset('storage/' . $dok->file_path) : null,

                ];
            }) ?? [],


            'gaji' => $this->gaji ? 'Rp ' . number_format($this->gaji, 0, ',', '.') : 'Rp 0',
            'uangLembur' => $this->uangLembur ? 'Rp ' . number_format($this->uangLembur, 0, ',', '.') : 'Rp 0',
            'dendaTerlambat' => $this->dendaTerlambat ? 'Rp ' . number_format($this->dendaTerlambat, 0, ',', '.') : 'Rp 0',
            'TotalGaji' => $this->TotalGaji ? 'Rp ' . number_format($this->TotalGaji, 0, ',', '.') : 'Rp 0',





        ];
    }


    private function position_name()
    {
        // Cukup cek kalau relasi position sudah ada
        return $this->position ? $this->position->name : null;
    }
}
