<?php

namespace App\Http\Controllers\Attendance;

use App\Helpers\LocationUtils;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClockInRequest;
use App\Http\Requests\ClockOutRequest;
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

    public function clockIn(ClockInRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();
        $checkClockSetting = CheckClockSetting::where('id', $user->id_check_clock_setting)
            ->with('checkClockSettingTime')
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: "Resource not found",
                code: 404
            );
        }

        date_default_timezone_set('Asia/Jakarta');
        $today = date('l');

        $id_ck_setting_time = null;

        foreach ($checkClockSetting->checkClockSettingTime as $settingTime) {
            if ($settingTime->day === $today) {
                $id_ck_setting_time = $settingTime->id;
                break;
            }
        }

        if (!$id_ck_setting_time) {
            return BaseResponse::error(
                message: "No setting time found for today",
                code: 404
            );
        }

        // check if ck setting requires location
        $location_lat = null;
        $location_lng = null;

        if ($checkClockSetting->location_lat && $checkClockSetting->location_lng && $checkClockSetting->radius) {
            $location_lat = $data['location_lat'];
            $location_lng = $data['location_lng'];

            // Validate location within radius
            $distanceMeters = LocationUtils::calculateDistance(
                $location_lat,
                $location_lng,
                $checkClockSetting->location_lat,
                $checkClockSetting->location_lng,
                'm'
            );

            if ($distanceMeters > $checkClockSetting->radius) {
                return BaseResponse::error(
                    message: "Location is outside the allowed radius",
                    code: 400
                );
            }
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $user->id_check_clock_setting,
                'id_ck_setting_time' => $id_ck_setting_time,
            ],
            [
                'location_lat' => $location_lat,
                'location_lng' => $location_lng,
                'clock_in' => now(),
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Clock in successful',
            code: 200
        );
    }

    public function clockOut(ClockOutRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();
        $checkClockSetting = CheckClockSetting::where('id', $user->id_check_clock_setting)
            ->with('checkClockSettingTime')
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: "Resource not found",
                code: 404
            );
        }

        date_default_timezone_set('Asia/Jakarta');
        $today = date('l');

        $ck_setting_time = null;

        foreach ($checkClockSetting->checkClockSettingTime as $settingTime) {
            if ($settingTime->day === $today) {
                $ck_setting_time = $settingTime;
                break;
            }
        }

        if (!$ck_setting_time) {
            return BaseResponse::error(
                message: "No setting time found for today",
                code: 404
            );
        }

        // check if ck setting requires location
        $location_lat = null;
        $location_lng = null;

        if ($checkClockSetting->location_lat && $checkClockSetting->location_lng && $checkClockSetting->radius) {
            $location_lat = $data['location_lat'];
            $location_lng = $data['location_lng'];

            // Validate location within radius
            $distanceMeters = LocationUtils::calculateDistance(
                $location_lat,
                $location_lng,
                $checkClockSetting->location_lat,
                $checkClockSetting->location_lng,
                'm'
            );

            if ($distanceMeters > $checkClockSetting->radius) {
                return BaseResponse::error(
                    message: "Location is outside the allowed radius",
                    code: 400
                );
            }
        }

        $checkClock = CheckClock::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_ck_setting' => $user->id_check_clock_setting,
                'id_ck_setting_time' => $ck_setting_time->id,
            ],
            [
                'location_lat' => $location_lat,
                'location_lng' => $location_lng,
                'clock_out' => now(),
                'break_start' => $ck_setting_time->break_start,
                'break_end' => $ck_setting_time->break_end,
            ]
        );

        return BaseResponse::success(
            data: $checkClock,
            message: 'Clock out successful',
            code: 200
        );
    }


}
