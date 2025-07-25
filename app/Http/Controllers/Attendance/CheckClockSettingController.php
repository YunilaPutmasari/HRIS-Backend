<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckClockSettingCreateRequest;
use App\Http\Requests\CheckClockSettingCompleteCreateRequest;
use App\Http\Requests\CheckClockSettingCompleteUpdateRequest;
use App\Http\Requests\CheckClockSettingUpdateRequest;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Org\User;
use App\Http\Responses\BaseResponse;
use Illuminate\Http\Request;

class CheckClockSettingController extends Controller
{
    public function index(Request $request)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $checkClockSettings = CheckClockSetting::whereIn('id_company', $companyIds)->get();
        return BaseResponse::success(
            data: $checkClockSettings,
            message: 'Check clock settings retrieved successfully',
            code: 200
        );
    }

    public function show(Request $request, $id_ck_setting)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $company = $user->companies()->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        $checkClockSetting = CheckClockSetting::where('id', $id_ck_setting)
            ->where('id_company', $company->id)
            ->with('checkClockSettingTime')
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: 'Check clock setting not found',
                code: 404
            );
        }

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting retrieved successfully',
            code: 200
        );
    }

    public function new(CheckClockSettingCreateRequest $request)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
        // $company = $user->companies()->where('id', $data['id_company'])->first();
        $company = $user->companies()->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        $checkClockSetting = CheckClockSetting::create([
            'name' => $data['name'],
            // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
            // 'id_company' => $data['id_company'],
            'id_company' => $company->id,
            'type' => $data['type'],
        ]);

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting created successfully',
            code: 201
        );
    }

    public function completeNew(CheckClockSettingCompleteCreateRequest $request)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
        // $company = $user->companies()->where('id', $data['id_company'])->first();
        $company = $user->companies()->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        // check if location_lat and location_lng are provided
        $location_lat = null;
        $location_lng = null;
        $radius = null;

        if (isset($data['location_lat']) && isset($data['location_lng']) && isset($data['radius'])) {
            $location_lat = $data['location_lat'];
            $location_lng = $data['location_lng'];
            $radius = $data['radius'];
        }

        $checkClockSetting = CheckClockSetting::create([
            'name' => $data['name'],
            // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
            // 'id_company' => $data['id_company'],
            'id_company' => $company->id,
            'type' => $data['type'],
            'location_lat' => $location_lat,
            'location_lng' => $location_lng,
            'radius' => $radius,
        ]);

        foreach ($data['check_clock_setting_time'] as $time) {
            $checkClockSetting->checkClockSettingTime()->create([
                'day' => $time['day'],
                'clock_in' => $time['clock_in'],
                'clock_out' => $time['clock_out'],
                'break_start' => $time['break_start'],
                'break_end' => $time['break_end']
            ]);
        }

        // update users with this check clock setting
        if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            User::whereIn('id', $data['user_ids'])
                ->update(['id_check_clock_setting' => $checkClockSetting->id]);
        }

        $checkClockSetting->load('checkClockSettingTime');

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting created successfully',
            code: 201
        );
    }

    public function update(CheckClockSettingUpdateRequest $request, $id_ck_setting)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companiesIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companiesIds)
            ->where('id', $id_ck_setting)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: 'Check clock setting not found',
                code: 404
            );
        }

        $checkClockSetting->update($data);

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting updated successfully',
            code: 200
        );
    }

    function completeUpdate(CheckClockSettingCompleteUpdateRequest $request, $id_ck_setting)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $company = $user->companies()->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        // check if location_lat and location_lng are provided
        $location_lat = null;
        $location_lng = null;
        $radius = null;

        if (isset($data['location_lat']) && isset($data['location_lng']) && isset($data['radius'])) {
            $location_lat = $data['location_lat'];
            $location_lng = $data['location_lng'];
            $radius = $data['radius'];
        }

        $checkClockSetting = CheckClockSetting::where('id', $id_ck_setting)
            ->where('id_company', $company->id)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: 'Check clock setting not found',
                code: 404
            );
        }

        $checkClockSetting->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'location_lat' => $location_lat,
            'location_lng' => $location_lng,
            'radius' => $radius,
        ]);

        // Clear existing check clock setting times
        $checkClockSetting->checkClockSettingTime()->delete();

        foreach ($data['check_clock_setting_time'] as $time) {
            $checkClockSetting->checkClockSettingTime()->create([
                'day' => $time['day'],
                'clock_in' => $time['clock_in'],
                'clock_out' => $time['clock_out'],
                'break_start' => $time['break_start'],
                'break_end' => $time['break_end'],
            ]);
        }

        // update users with this check clock setting
        if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            User::whereIn('id', $data['user_ids'])
                ->update(['id_check_clock_setting' => $checkClockSetting->id]);
        }

        $checkClockSetting->load('checkClockSettingTime');

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting updated successfully',
            code: 200
        );
    }

    public function delete(Request $request, $id_ck_setting)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companiesIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companiesIds)
            ->where('id', $id_ck_setting)
            ->first();

        if (!$checkClockSetting) {
            return BaseResponse::error(
                message: 'Check clock setting not found',
                code: 404
            );
        }

        $checkClockSetting->delete();

        return BaseResponse::success(
            data: null,
            message: 'Check clock setting deleted successfully',
            code: 200
        );
    }
}
