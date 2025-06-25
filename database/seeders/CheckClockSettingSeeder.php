<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Org\User;
use App\Models\Org\Company;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Attendance\CheckClockSettingTime;

class CheckClockSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Cari perusahaan Anonim Corp
        $company = Company::where('name', 'Anonim Corp')->first();

        if (!$company) {
            echo "Company 'Anonim Corp' tidak ditemukan.\n";
            return;
        }

        // Buat satu CheckClockSetting
        $checkClockSetting = CheckClockSetting::create([
            'id' => Str::uuid(),
            'id_company' => $company->id,
            'name' => 'Shift Pagi',
            'type' => 'WFO', // Bisa ganti jadi WFA / Hybrid
        ]);

        echo "CheckClockSetting '{$checkClockSetting->name}' berhasil dibuat untuk {$company->name}\n";

        // Jadwal harian (Senin - Jumat)
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        foreach ($days as $day) {
            CheckClockSettingTime::create([
                'id' => Str::uuid(),
                'id_ck_setting' => $checkClockSetting->id,
                'day' => $day,
                'clock_in' => '09:00:00',
                'clock_out' => '17:00:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
            ]);

            echo "CheckClockSettingTime untuk hari $day berhasil dibuat\n";
        }
    }
}
