<?php

namespace App\Http\Controllers\Overtime;

use App\Http\Controllers\Controller;
use App\Http\Requests\OvertimeSettingStoreRequest;
use App\Http\Requests\OvertimeSettingUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Overtime\OvertimeSetting;
use Illuminate\Support\Facades\DB;
use Throwable;

class OvertimeSettingController extends Controller
{
    public function index()
    {
        try {
            $settings = OvertimeSetting::with('rules')->latest()->get();

            return BaseResponse::success($settings, 'Overtime settings retrieved successfully');
        } catch (Throwable $e) {
            return BaseResponse::error(null, 'Failed to retrieve overtime settings: ' . $e->getMessage(), 500);
        }
    }

    public function store(OvertimeSettingStoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $setting = DB::transaction(function () use ($validated) {
                $overtimeSetting = OvertimeSetting::create([
                    'name' => $validated['name'],
                    'source' => $validated['source'],
                ]);

                if (!empty($validated['rules'])) {
                    $overtimeSetting->rules()->createMany($validated['rules']);
                }

                return $overtimeSetting;
            });

            $setting->load('rules');

            return BaseResponse::success($setting, 'Overtime settings created successfully', 201);
        } catch (Throwable $e) {
            return BaseResponse::error(null, 'Failed to create overtime settings: ' . $e->getMessage());
        }
    }

    public function show(OvertimeSetting $overtimeSetting)
    {
        try {
            // Mengambil satu data setting beserta rules-nya
            $overtimeSetting->load('rules');
            return BaseResponse::success($overtimeSetting, 'Overtime setting retrieved successfully');
        } catch (Throwable $e) {
            return BaseResponse::error(null, 'Overtime setting not found: ' . $e->getMessage(), 404);
        }
    }

    public function update(OvertimeSettingUpdateRequest $request, OvertimeSetting $overtimeSetting)
    {
        $validated = $request->validated();

        try {
            $setting = DB::transaction(function () use ($validated, $overtimeSetting) {
                // 1. Update data OvertimeSetting utama
                $overtimeSetting->update([
                    'name' => $validated['name'],
                    'source' => $validated['source'],
                    'is_active' => $validated['is_active'],
                ]);

                // 2. Hapus semua rules yang lama
                $overtimeSetting->rules()->delete();

                // 3. Buat ulang semua rules dari data request yang baru
                if (!empty($validated['rules'])) {
                    $overtimeSetting->rules()->createMany($validated['rules']);
                }

                return $overtimeSetting;
            });

            $setting->load('rules');

            return BaseResponse::success($setting, 'Overtime setting updated successfully');
        } catch (Throwable $e) {
            return BaseResponse::error(null, 'Failed to update overtime setting: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(OvertimeSetting $overtimeSetting)
    {
        try {
            // Soft delete akan digunakan jika model menggunakan trait SoftDeletes
            $overtimeSetting->delete();
            return BaseResponse::success(null, 'Overtime setting deleted successfully');
        } catch (Throwable $e) {
            return BaseResponse::error(null, 'Failed to delete overtime setting: ' . $e->getMessage(), 500);
        }
    }
}
