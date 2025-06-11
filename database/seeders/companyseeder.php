<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;  // ✅ Tambahkan ini
use Illuminate\Support\Str;


class companyseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil salah satu user sebagai manager, jika ada
        $manager = DB::table('tb_user')->first();

        if (!$manager) {
            // Jika tidak ada user, buat ID dummy
            $managerId = Str::uuid();
        } else {
            $managerId = $manager->id;
        }

        DB::table('tb_company')->insert([
            'id' => Str::uuid(),
            'name' => 'PT Contoh Sukses',
            'id_manager' => $managerId,
            'id_subscription' => Str::uuid(), // atau null jika belum ada subscription
            'effective_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
