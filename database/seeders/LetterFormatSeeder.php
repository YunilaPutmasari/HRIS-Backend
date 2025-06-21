<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class LetterFormatSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_letter_format')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Surat Keterangan Kerja',
                'description' => 'Surat resmi keterangan kerja',
                'template' => '<p>Dengan ini menyatakan bahwa {{nama}} benar bekerja di perusahaan kami sejak {{tanggal_masuk}}.</p>',
                'type' => 'other', // ubah ke 'other'
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Permohonan Cuti',
                'description' => 'Format permohonan cuti karyawan',
                'template' => '<p>Saya yang bertanda tangan di bawah ini, {{nama}}, mengajukan cuti selama {{jumlah_hari}} hari terhitung mulai {{tanggal_mulai}}.</p>',
                'type' => 'other', // ubah ke 'other'
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

    }
}
