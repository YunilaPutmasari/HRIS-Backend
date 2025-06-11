<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Org\User;
use App\Models\Org\EMployee;
use Illuminate\Support\Str;

class EmployeeSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = '01974d1b-f010-716a-8ede-be668ca7d657';
        $password = Hash::make('password');

        for ($i=1; $i<=20; $i++){
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
                'first_name' => "Karyawan",
                'last_name' => "Ke{$i}",
                'address' => "Jalan Karyawan No. {$i}, Kota Jakarta",
                'id_position' => NULL, // bisa isi jika ada relasi position
                'employment_status' => in_array($i, [1, 5, 9, 12]) ? 'inactive' : 'active',
                'tipeKontrak' => match(true) {
                    in_array($i, [1,5]) => 'Lepas',
                    in_array($i, [2,4,9,12]) => 'Kontrak',
                    default => 'Tetap',
                },
            ]);
        }
    }
}
