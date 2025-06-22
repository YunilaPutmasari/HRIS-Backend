<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Attendance\CheckClockSettingController;
use App\Http\Controllers\Attendance\CheckClockSettingTimeController;
use App\Http\Controllers\Attendance\CheckClockController;
use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Org\EmployeeController;
use App\Http\Controllers\Org\DeptPositionsController;
use App\Http\Controllers\Org\DepartmentsController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Lettering\ApprovalController;
use App\Http\Controllers\Lettering\LetterController;
use App\Http\Controllers\Lettering\LetterFormatController;

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
            Route::get('/{id_ck_setting}', [CheckClockSettingController::class, 'show'])->name('show');

            Route::post('/new', [CheckClockSettingController::class, 'new'])->name('new');
            Route::post('/complete-new', [CheckClockSettingController::class, 'completeNew'])->name('complete-new');

            Route::put('/update/{id_ck_setting}', [CheckClockSettingController::class, 'update'])->name('update');
            Route::put('/complete-update/{id_ck_setting}', [CheckClockSettingController::class, 'completeUpdate'])->name('complete-update');
            Route::put('/{id_ck_setting}/update/{id_ck_setting_time}', [CheckClockSettingTimeController::class, 'update'])->name('update');

            Route::delete('/delete/{id_ck_setting}', [CheckClockSettingController::class, 'delete'])->name('delete');
            Route::delete('/{id_ck_setting}/delete/{id_ck_setting_time}', [CheckClockSettingTimeController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'check-clock',
            'as' => 'check-clock.',
        ], function () {
            Route::get('/employee-check-clocks', [CheckClockController::class, 'employeeCheckClocks'])->name('employee-check-clocks');
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
        Route::post('xendit/callback', [InvoiceController::class, 'handleXenditCallback']);
        Route::get('redirect', [InvoiceController::class, 'paymentRedirect']);
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
        'prefix' => 'employees',
        'as' => 'employees.',
    ], function () {
        Route::get('/comp-employees', [EmployeeController::class, 'getEmployeeBasedCompany'])->name('getEmployeeBasedCompany');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{id}', [EmployeeController::class, 'getEmployeeById']);
        Route::put('/{id}', [EmployeeController::class, 'updateEmployee']);
        Route::post('/{id}/upload-document', [EmployeeController::class, 'uploadDocument']);

        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::delete('user/{id}/document/{id_document}', [EmployeeController::class, 'deleteEmployeeDocument'])->name('deleteEmployeeDocument');
        Route::post('/import', [EmployeeController::class, 'import'])->name('import');
        Route::group([
            'prefix' => 'letter',
            'as' => 'letter.',
        ], function () {
            Route::get('/formats', [LetterController::class, 'getFormats'])->name('getFormats');
            Route::post('/', [LetterController::class, 'store'])->name('store');
        });
        Route::group([
            'prefix' => 'dashboard',
            'as' => 'dashboard.',
        ], function () {
            Route::get('/getEmployee', [EmployeeController::class, 'getEmployee'])->name('getEmployee');
            Route::get('/contract-stats', [EmployeeController::class, 'getEmployeeContractStats'])->name('getEmployeeContractStats'); //asumsiku tipeKontrak: Tetap,Kontrak,Lepas
            Route::get('/status-stats', [EmployeeController::class, 'getEmployeeStatusStats'])->name('getEmployeeStatusStats'); //asumsiku tipeKontrak: Tetap,Kontrak,Lepas
            Route::get('/recent-approvals', [ApprovalController::class, 'getRecentApprovals'])->name('getRecentApprovals');
        });
    });

    Route::group([
        'prefix' => 'positions',
        'as' => 'positions.',
    ], function () {
        Route::get('/', [DeptPositionsController::class, 'index'])->name('index');
        Route::post('/', [DeptPositionsController::class, 'store'])->name('store');
        Route::get('/by-department/{id_department}', [DeptPositionsController::class, 'getByDepartment'])->name('getByDepartment');
        Route::post('/by-department/{id_department}', [DeptPositionsController::class, 'storeByDepartment'])->name('storeByDepartment');
        Route::get('/{id}', [DeptPositionsController::class, 'show'])->name('show');
        Route::put('/{id}', [DeptPositionsController::class, 'update'])->name('update');
        Route::delete('/{id}', [DeptPositionsController::class, 'destroy'])->name('destroy');
    });

    Route::group([
        'prefix' => 'departments',
        'as' => 'departments.',
    ], function () {
        Route::get('/', [DepartmentsController::class, 'index'])->name('index');
        Route::post('/', [DepartmentsController::class, 'store'])->name('store');
        Route::get('/{id}', [DepartmentsController::class, 'show'])->name('show');
        Route::put('/{id}', [DepartmentsController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartmentsController::class, 'destroy'])->name('destroy');
    });
    
    Route::group([
        'prefix' => 'company',
        'as' => 'company.',
    ], function () {
        Route::get('/', [DepartmentsController::class, 'getCompanyData']);
    });
});

Route::group([
    'prefix' => 'admin/subscription',
    'as' => 'admin.subscription',
    'middleware' => ['auth:sanctum', 'admin'],
], function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/request-change', [SubscriptionController::class, 'requestChange']);
    Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription']);
    Route::get('/',[SubscriptionController::class,'getAllSubscription']);
    Route::get('/active',[SubscriptionController::class,'getActiveSubscription']);
    Route::get('/current',[SubscriptionController::class,'getCurrentSubscription']);
    Route::get('/invoices',[SubscriptionController::class,'getCompanyInvoices']);
    Route::get('/packageTypes',[SubscriptionController::class,'getAllPackageTypes']);
    Route::get('/invoices/{invoice_id}',[SubscriptionController::class,'getInvoiceDetail']);
    Route::get('/{subscription_id}',[SubscriptionController::class,'getUsageBySubscription']);
});

