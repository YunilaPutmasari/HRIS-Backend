<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Attendance\CheckClock;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Attendance\CheckClockSettingTime;
use App\Models\Org\User;
use App\Models\Org\Company;

class CheckClockSeeder extends Seeder
{
    public function run(): void
    {
        // CheckClock::truncate();
        // Ganti ID atau nama sesuai kebutuhan
        $company = Company::where('name', 'Anonim Corp')->first(); // atau ->find($id)

        if (!$company) {
            echo "Company tidak ditemukan.\n";
            return;
        }

        $setting = CheckClockSetting::first();
        $settingTime = CheckClockSettingTime::first();

        if (!$setting || !$settingTime) {
            echo "CheckClockSetting atau SettingTime belum ada.\n";
            return;
        }

        // Loop semua user dari company ini
        foreach ($company->users as $user) {
            CheckClock::create([
                'id' => Str::uuid(),
                'id_user' => $user->id,
                'id_ck_setting' => $setting->id,
                'id_ck_setting_time' => $settingTime->id,
                'clock_in' => Carbon::parse('08:00:00')->addMinutes(rand(0,45)),
                'break_start' => Carbon::parse('12:00:00'),
                'break_end' => Carbon::parse('13:00:00'),
                'clock_out' => Carbon::parse('17:00:00'),
            ]);
        }

        echo "Seeder CheckClock untuk user di perusahaan '{$company->name}' berhasil dijalankan.\n";
    }
}
