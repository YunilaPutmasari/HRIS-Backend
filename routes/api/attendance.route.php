<?php

use App\Http\Controllers\Attendance\CheckClockController;

Route::group([
    'prefix' => 'attendance',
    'as' => 'attendance.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::group([
        'prefix' => 'check-clock',
        'as' => 'check-clock.',
    ], function () {
        Route::get('/self', [CheckClockController::class, 'selfCheckClocks'])->name('self');
        Route::get('/clock-in/{id_ck_setting}/{id_ck_setting_time}', [CheckClockController::class, 'clockIn'])->name('clock-in');
        Route::get('/clock-out/{id_ck_setting}/{id_ck_setting_time}', [CheckClockController::class, 'clockOut'])->name('clock-out');
        Route::get('/break-start/{id_ck_setting}/{id_ck_setting_time}', [CheckClockController::class, 'breakStart'])->name('break-start');
        Route::get('/break-end/{id_ck_setting}/{id_ck_setting_time}', [CheckClockController::class, 'breakEnd'])->name('break-end');
    });
});


