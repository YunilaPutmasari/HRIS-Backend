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

        // Ambil check_clock_setting milik perusahaan ini
        $checkClockSetting = CheckClockSetting::where('id_company', $company->id)->first();

        if (!$checkClockSetting) {
            echo "CheckClockSetting tidak ditemukan untuk perusahaan ini.\n";
            return;
        }

        // Ambil semua jadwal harian (Senin - Jumat)
        $checkClockSettingTimes = CheckClockSettingTime::where('id_ck_setting', $checkClockSetting->id)
            ->whereIn('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
            ->get();
        
        if ($checkClockSettingTimes->isEmpty()) {
            echo "Jadwal waktu kerja belum dibuat untuk setting ini.\n";
            return;
        }        

        // Loop semua user dari company ini
        foreach ($company->users as $user) {
            for($i = 0; $i < 20; $i++) {
                $randomSettingTime = $checkClockSettingTimes->random();

                $clockIn = Carbon::parse($randomSettingTime->clock_in)->addMinutes(rand(0, 45));
                $breakStart = Carbon::parse($randomSettingTime->break_start);
                $breakEnd = Carbon::parse($randomSettingTime->break_end);
                $clockOut = Carbon::parse($randomSettingTime->clock_out);

                $status = rand(1, 10) === 1 ? 'late' : 'on-time';
                
                CheckClock::create([
                    'id' => Str::uuid(),
                    'id_user' => $user->id,
                    'id_ck_setting' => $checkClockSetting->id,
                    'id_ck_setting_time' => $randomSettingTime->id,
                    'clock_in' => $clockIn->toTimeString(),
                    'break_start' => $breakStart->toTimeString(),
                    'break_end' => $breakEnd->toTimeString(),
                    'clock_out' => $clockOut->toTimeString(),
                    'status' => $status
                ]);
                echo "Seeder CheckClock ke '{$i}'.\n";
            }
        }

        echo "Seeder CheckClock untuk user di perusahaan '{$company->name}' berhasil dijalankan.\n";
    }
}
