<?php


use App\Http\Controllers\Overtime\OvertimeController;
use App\Http\Controllers\Overtime\OvertimeSettingController;

Route::group([
    'prefix' => 'overtime-settings',
    'as' => 'overtime-settings.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::apiResource('/', OvertimeSettingController::class);
});

Route::group([
    'prefix' => 'overtime',
    'as' => 'overtime.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::apiResource('/', OvertimeController::class);
    Route::patch('{overtime}/approve', [OvertimeController::class, 'approve']);
    Route::patch('{overtime}/reject', [OvertimeController::class, 'reject']);
});
