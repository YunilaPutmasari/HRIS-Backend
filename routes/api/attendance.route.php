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
        Route::get('/self-ck-setting', [CheckClockController::class, 'selfCheckClockSetting'])->name('self-ck-setting');
        Route::get('/clock-in', [CheckClockController::class, 'clockIn'])->name('clock-in');
        Route::get('/clock-out', [CheckClockController::class, 'clockOut'])->name('clock-out');
        Route::get('/break-start', [CheckClockController::class, 'breakStart'])->name('break-start');
        Route::get('/break-end', [CheckClockController::class, 'breakEnd'])->name('break-end');
    });
});


