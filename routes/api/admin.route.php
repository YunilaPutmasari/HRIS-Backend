<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Attendance\CheckClockSettingController;
use App\Http\Controllers\Attendance\CheckClockSettingTimeController;
use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Org\EmployeeController;
use App\Http\Controllers\Subscription\SubscriptionController;

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

    //Bagian payment hanya bisa diakses admin atau HR
    Route::group([
        'prefix' => 'invoices',
        'as' => 'invoices.',
    ], function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    Route::group([
        'prefix' => 'payments',
        'as' => 'payments.',
    ], function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
        Route::put('/{id}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
    });

    Route::group([
        'prefix'=>'employees',
        'as' => 'employees.',
    ], function(){
        Route::group([
            'prefix'=>'dashboard',
            'as'=>'dashboard.',
        ], function () {
            Route::get('/getEmployee',[EmployeeController::class, 'getEmployee'])->name('getEmployee');
            Route::get('/contract-stats',[EmployeeController::class, 'getEmployeeContractStats'])->name('getEmployeeContractStats'); //asumsiku tipeKontrak: Tetap,Kontrak,Lepas
            Route::get('/status-stats',[EmployeeController::class, 'getEmployeeStatusStats'])->name('getEmployeeStatusStats'); //asumsiku tipeKontrak: Tetap,Kontrak,Lepas
        });
    });
});

Route::group([
    'prefix' => 'admin/subscription',
    'as' => 'admin.subscription',
    'middleware' => ['auth:sanctum', 'admin'],
], function() {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::post('/', [SubscriptionController::class, 'store'])->name('store');
    Route::put('/{id}', [SubscriptionController::class, 'update'])->name('update');
    Route::post('/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});
