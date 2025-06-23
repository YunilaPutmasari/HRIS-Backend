<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Models\Subscription\PackageType;
use App\Models\Subscription\Subscription;

class OrganizationSeeder2 extends Seeder
{
    public function run(): void
    {
        // Buat User Manager
        $user = User::create([
            'id' => (string) Str::uuid(),
            'email' => 'AnonimTester@email.com',
            'phone_number' => '+6999999999',
            'password' => Hash::make('password'),
            'is_admin' => '1', // dia manager
        ]);

        // Buat Company dan hubungkan manager
        $company = Company::create([
            'id' => (string) Str::uuid(),
            'name' => 'Anonim Corp',
            'address' => 'Jl.Mandiri Semua Blok.IV No.12, Malang, Jawa Timur',
            'id_manager' => $user->id,
        ]);

        // Hubungkan user ke company sebagai tempat kerja
        $user->update([
            'id_workplace' => $company->id,
        ]);

        // Buat data karyawan untuk manager
        Employee::create([
            'id' => (string) Str::uuid(),
            'id_user' => $user->id,
            'sign_in_code' => Str::upper(Str::random(6)),
            'id_position' => null,
            'first_name' => 'Anonim',
            'last_name' => 'Tester',
            'nik' => '000001',
            'address' => 'Jl.Inisiatif No.7 Malang, Jawa Timur',
            'tempat_lahir' => 'Malang',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'pendidikan' => 'S1',
            'no_telp' => '+6999999999',
            'start_date' => now()->toDateString(),
            'employment_status' => 'active',
            'tipe_kontrak' => 'Tetap',
            'cabang' => 'Cabang Malang',
            'tanggal_efektif' => now()->toDateString(),
            'bank' => 'BCA',
            'no_rek' => '1234567890',
            'dokumen' => null,
            'avatar' => null,
        ]);

        // Tambahkan subscription default (free)
        $freePlan = PackageType::where('is_free', true)->first();

        if (!$freePlan) {
            $this->command->error("PackageType free tidak ditemukan.");
            return;
        }

        $subscription = Subscription::create([
            'id' => (string) Str::uuid(),
            'id_company' => $company->id,
            'id_package_type' => $freePlan->id,
            'seats' => $freePlan->max_seats,
            'is_trial' => true,
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(10),
            'status' => 'active',
        ]);

        // Update company dengan subscription ID
        $company->update([
            'id_subscription' => $subscription->id,
            'has_used_trial' => true
        ]);

        $this->command->info("Seeder selesai: User, Company, Employee, dan Subscription berhasil dibuat.");
    }
}
