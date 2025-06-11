<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Org\User;
use App\Models\Org\Employee;
use Illuminate\Support\Str;

class EmployeeSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = '01975f52-b27c-72eb-a539-ecff21c032f7';
        $password = Hash::make('password');

        for ($i=7; $i<=20; $i++){
            $user = User::create([
                'id' => (string) Str::uuid(),
                'email' => "employee{$i}@example.com",
                'phone_number' => "+1234567890{$i}",
                'password' => $password,
                'is_admin' => '0', // bukan admin, jadi employee
                'id_workplace' => $companyId,
            ]);

            Employee::create([
                'id' => (string) Str::uuid(),
                'id_user' => $user->id,
                'sign_in_code' => Str::random(6),
                'id_position' => NULL,
                'first_name' => "Karyawan",
                'last_name' => "Ke{$i}",
                'nik' => sprintf('%06d', $i),
                'address' => "Jalan Karyawan No. {$i}, Kota Jakarta",
                'tempat_lahir' => "Jakarta",
                'tanggal_lahir' => "1990-01-0$i",
                'jenis_kelamin' => $i % 2 == 0 ? 'Laki-laki' : 'Perempuan',
                'pendidikan' => ['SMA', 'D3', 'S1'][$i % 3],
                'no_telp' => "08123456789$i",
                'start_date' => "2020-01-0$i",
                'end_date' => in_array($i, [1, 5, 9]) ? "2023-12-31" : null,
                'employment_status' => in_array($i, [1, 5, 9, 12]) ? 'inactive' : 'active',
                'tipe_kontrak' => match(true) {
                    in_array($i, [1,5]) => 'Lepas',
                    in_array($i, [2,4,9,12]) => 'Kontrak',
                    default => 'Tetap',
                },
                'cabang' => "Cabang Jakarta",
                'employment_status' => in_array($i, [1, 5, 9, 12]) ? 'inactive' : 'active',
                'tanggal_efektif' => now()->addDays(7)->toDateString(),
                'bank' => "BCA",
                'no_rek' => "123456789" . $i,
                'dokumen' => null,
                'avatar' => null,
            ]);
        }
    }
}
