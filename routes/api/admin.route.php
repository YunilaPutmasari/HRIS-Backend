<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Attendance\CheckClockSettingController;

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
            Route::put('/update/{id}', [CheckClockSettingController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CheckClockSettingController::class, 'delete'])->name('delete');
        });
    });
});
