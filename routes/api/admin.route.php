<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Attendance\CheckClockSettingController;
use App\Http\Controllers\Attendance\CheckClockSettingTimeController;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth:sanctum', 'admin'],
], function () {
    Route::group([
        'prefix' => 'attendance',
        'as' => 'attendance.',
    ], function () {
        Route::group([
            'prefix' => 'check-clock-setting',
            'as' => 'check-clock-setting.',
        ], function () {
            Route::get('/', [CheckClockSettingController::class, 'index'])->name('index');

            Route::post('/new', [CheckClockSettingController::class, 'new'])->name('new');
            Route::post('/{id_ck_setting}/new', [CheckClockSettingTimeController::class, 'new'])->name('new');

            Route::put('/update/{id_ck_setting}', [CheckClockSettingController::class, 'update'])->name('update');
            Route::put('/{id_ck_setting}/update/{id_ck_setting_time}', [CheckClockSettingTimeController::class, 'update'])->name('update');

            Route::delete('/delete/{id_ck_setting}', [CheckClockSettingController::class, 'delete'])->name('delete');
            Route::delete('/{id_ck_setting}/delete/{id_ck_setting_time}', [CheckClockSettingTimeController::class, 'delete'])->name('delete');
        });
        Route::group([
            'prefix' => 'check-clock-setting-time',
            'as' => 'check-clock-setting-time.',
        ], function () {
        });
    });
});
