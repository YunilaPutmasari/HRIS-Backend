<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckClockSettingCreateRequest;
use App\Http\Requests\CheckClockSettingUpdateRequest;
use App\Models\Attendance\CheckClockSetting;
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

    public function new(CheckClockSettingCreateRequest $request)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $company = $user->companies()->where('id', $data['id_company'])->first();

        if (!$company) {
            return BaseResponse::error(
                message: "You don't have permission to access this company",
                code: 404
            );
        }

        $checkClockSetting = CheckClockSetting::create([
            'name' => $data['name'],
            'id_company' => $data['id_company'],
            'type' => $data['type'],
        ]);

        return BaseResponse::success(
            data: $checkClockSetting,
            message: 'Check clock setting created successfully',
            code: 201
        );
    }

    public function update(CheckClockSettingUpdateRequest $request, $id)
    {
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companiesIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companiesIds)
            ->where('id', $id)
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

    public function delete(Request $request, $id)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companiesIds = $companies->pluck('id')->toArray();

        $checkClockSetting = CheckClockSetting::whereIn('id_company', $companiesIds)
            ->where('id', $id)
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
