<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Attendance\CheckClockSettingTime;
use App\Models\Attendance\CheckClock;
use App\Http\Responses\BaseResponse;
use Illuminate\Http\Request;

class CheckClockController extends Controller
{

    public function selfCheckClockSetting(Request $request)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $checkClockSetting = CheckClockSetting::where('id', $user->id_check_clock_setting)
            ->with('checkClockSettingTime')
            ->first();

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Self check clock setting retrieved successfully',
            code: 200
        );
    }

    public function employeeCheckClocks(Request $request)
    {
        // all employees of user's managed company check clocks
        $user = $request->user();
        $company = $user->companies()->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        $checkClocksSettingIds = CheckClockSetting::where('id_company', $company->id)
            ->pluck('id')
            ->toArray();

        $checkClocks = CheckClock::whereIn('id_ck_setting', $checkClocksSettingIds)
            ->with('user', 'checkClockSetting', 'checkClockSettingTime')
            ->get();

        return BaseResponse::success(
            data: $checkClocks,
            message: 'Employee check clocks retrieved successfully',
            code: 200
        );
    }

    public function selfCheckClocks(Request $request)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $checkClocks = $user->checkClocks()
            ->with('checkClockSettingTime')
            ->get();

        return BaseResponse::success(
            data: $checkClocks,
            message: 'Self check clocks retrieved successfully',
            code: 200
        );
    }

    public function clockIn(Request $request, string $id_ck_setting, string $id_ck_setting_time)
    {
        $user = $request->user();
        $checkClockSettingTime = CheckClockSettingTime::where('id', $id_ck_setting_time)
            ->where('id_ck_setting', $id_ck_setting)
            ->first();


        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $id_ck_setting,
                'id_ck_setting_time' => $id_ck_setting_time,
            ],
            [
                'clock_in' => now(),
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Clock in successful',
            code: 200
        );
    }

    public function clockOut(Request $request, string $id_ck_setting, string $id_ck_setting_time)
    {
        $user = $request->user();
        $checkClockSettingTime = CheckClockSettingTime::where('id', $id_ck_setting_time)
            ->where('id_ck_setting', $id_ck_setting)
            ->first();


        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $id_ck_setting,
                'id_ck_setting_time' => $id_ck_setting_time,
            ],
            [
                'clock_out' => now(),
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Clock out successful',
            code: 200
        );
    }


    public function breakStart(Request $request, string $id_ck_setting, string $id_ck_setting_time)
    {
        $user = $request->user();
        $checkClockSettingTime = CheckClockSettingTime::where('id', $id_ck_setting_time)
            ->where('id_ck_setting', $id_ck_setting)
            ->first();


        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $id_ck_setting,
                'id_ck_setting_time' => $id_ck_setting_time,
            ],
            [
                'break_start' => now(),
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Break end successful',
            code: 200
        );
    }


    public function breakEnd(Request $request, string $id_ck_setting, string $id_ck_setting_time)
    {
        $user = $request->user();
        $checkClockSettingTime = CheckClockSettingTime::where('id', $id_ck_setting_time)
            ->where('id_ck_setting', $id_ck_setting)
            ->first();


        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $id_ck_setting,
                'id_ck_setting_time' => $id_ck_setting_time,
            ],
            [
                'break_end' => now(),
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Break end successful',
            code: 200
        );
    }

}
