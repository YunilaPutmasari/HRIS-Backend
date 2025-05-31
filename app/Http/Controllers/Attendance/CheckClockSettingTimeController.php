<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckClockSettingTimeUpdateRequest;
use App\Models\Attendance\CheckClockSettingTime;
use Illuminate\Http\Request;
use App\Http\Requests\CheckClockSettingTimeCreateRequest;
use App\Models\Attendance\CheckClockSetting;
use App\Http\Responses\BaseResponse;

class CheckClockSettingTimeController extends Controller
{

    public function new(CheckClockSettingTimeCreateRequest $request, $id_ck_setting)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companyIds)
            ->where('id', $id_ck_setting)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: "You don't have permission to access this check clock setting",
                code: 404
            );
        }

        $checkClockSettingTime = CheckClockSettingTime::create([
            'id_ck_setting' => $id_ck_setting,
            'day' => $data['day'],
            'clock_in' => $data['clock_in'],
            'clock_out' => $data['clock_out'],
            'break_start' => $data['break_start'],
            'break_end' => $data['break_end'],
        ]);

        return BaseResponse::success(
            data: $checkClockSettingTime,
            message: 'Check clock setting time created successfully',
            code: 201
        );
    }

    public function update(CheckClockSettingTimeUpdateRequest $request, $id_ck_setting)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companyIds)
            ->where('id', $id_ck_setting)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: "You don't have permission to access this check clock setting",
                code: 404
            );
        }

        $checkClockSettingTime = CheckClockSettingTime::where('id_ck_setting', $id_ck_setting)->first();

        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClockSettingTime->update($data);

        return BaseResponse::success(
            data: $checkClockSettingTime,
            message: 'Check clock setting time updated successfully',
            code: 200
        );
    }

    public function delete(Request $request, $id_ck_setting, $id_ck_setting_time)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companyIds)
            ->where('id', $id_ck_setting)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: "You don't have permission to access this check clock setting",
                code: 404
            );
        }

        $checkClockSettingTime = CheckClockSettingTime::where('id', $id_ck_setting_time)
            ->where('id_ck_setting', $id_ck_setting)
            ->first();

        if (!$checkClockSettingTime) {
            return BaseResponse::error(
                message: 'Check clock setting time not found',
                code: 404
            );
        }

        $checkClockSettingTime->delete();

        return BaseResponse::success(
            message: 'Check clock setting time deleted successfully',
            code: 200
        );
    }
}
