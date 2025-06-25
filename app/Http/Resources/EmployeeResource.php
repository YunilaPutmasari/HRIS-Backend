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
            'tempat_lahir' => $this->tempat_lahir ?? '',
            'tanggal_lahir' => $this->tanggal_lahir ?? '',
            'jenis_kelamin' => $this->jenis_kelamin ?? '',
            'pendidikan' => $this->pendidikan ?? '',
            'email' => $this->user?->email ?? '',
            'phone_number' => $this->user?->phone_number ?? '',

            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'tenure' => $this->tenure,
            'jadwal' => $this->jadwal ?? '',
            'tipe_kontrak' => $this->tipe_kontrak ?? '',
            'cabang' => $this->cabang ?? '',
            'employment_status' => $this->employment_status ?? '',
            'jabatan' => $this->position ? $this->position->name : null,
            'id_position' => $this->id_position,
            'id_department' => $this->id_department,
            'department' => $this->department ? $this->department->name : null,
            'tanggal_efektif' => $this->tanggal_efektif,
            'bank' => $this->bank ?? '',
            'no_rek' => $this->no_rek ?? '',
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,

            // 'dokumen' => $this->dokumen ? asset('storage/' . $this->dokumen) : null,
            'dokumen' => $this->documents?->map(function ($dok) {
                return [
                    'id' => $dok->id,
                    'name' => $dok->name,
                    'file' => $dok->file_path ? asset('storage/' . $dok->file_path) : null,

                ];
            }) ?? [],



            'gaji' => $this->position->gaji ? 'Rp ' . number_format($this->position->gaji, 0, ',', '.') : 'Rp 0',
            'uang_lembur' => $this->uang_lembur ? 'Rp ' . number_format($this->uang_lembur, 0, ',', '.') : 'Rp 0',
            'denda_terlambat' => $this->denda_terlambat ? 'Rp ' . number_format($this->denda_terlambat, 0, ',', '.') : 'Rp 0',
            'total_gaji' => $this->total_gaji ? 'Rp ' . number_format($this->total_gaji, 0, ',', '.') : 'Rp 0',

            'user' => [
                'email' => $this->user->email ?? null,
                'phone_number' => $this->user->phone_number ?? null,

                'id_check_clock_setting' => $this->user?->id_check_clock_setting ?? null,
                'jadwal_type' => $this->user?->checkClockSetting?->type ?? '-',
            ],





        ];
    }


    private function position_name()
    {
        // Cukup cek kalau relasi position sudah ada
        return $this->position ? $this->position->name : null;

    }
    private function department_name()
    {
        return $this->department ? $this->department->name : null;
    }


}
