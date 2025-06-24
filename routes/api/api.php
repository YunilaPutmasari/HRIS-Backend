<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Controllers\org\EmployeeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Payment\XenditWebhookController;
use App\Http\Controllers\EmployeeDashboardController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
    'middleware' => 'auth:sanctum'
], function () {

    // Employee dashboard data
    Route::group([
        'prefix' => 'employee',
        'as' => 'employee.',
    ], function () {
        Route::get('/dashboard', [EmployeeController::class, 'getEmployeeDashboard']);
        Route::get('/profile', [EmployeeController::class, 'getEmployeeProfile']);
        Route::get('/attendance', [EmployeeController::class, 'getEmployeeAttendance']);
        Route::get('/payroll', [EmployeeController::class, 'getEmployeePayroll']);
    });
});

Route::get('', function (Request $request) {
    return UserResource::collection(User::all());
});

// Route::middleware('auth:sanctum')->get('/employee/dashboard', [EmployeeDashboardController::class, 'index']);

Route::middleware(['auth:sanctum', 'manager.admin'])->group(function () {
    Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index']);
    Route::get('/manager/employee', [EmployeeController::class, 'index']);
    Route::get('/manager/attendance', [AttendanceController::class, 'index']);
    Route::get('/manager/approval', [ApprovalController::class, 'index']);
    Route::get('/manager/lettering', [LetteringController::class, 'index']);
    Route::get('/manager/jadwal', [ScheduleController::class, 'index']);
    Route::get('/manager/settings', [SettingController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'employee.only'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index']);
    Route::get('/employee/attendance', [AttendanceController::class, 'index']);
    Route::get('/employee/approval', [ApprovalController::class, 'index']);
    Route::get('/employee/overtime', [OvertimeController::class, 'index']);
    Route::get('/employee/lettering', [LetteringController::class, 'index']);
});


Route::post('/xendit/webhook/invoice', [XenditWebhookController::class, 'handle']);

require __DIR__ . '/auth.route.php';
require __DIR__ . '/admin.route.php';
require __DIR__ . '/attendance.route.php';
// require __DIR__ . '/user.route.php';
// require __DIR__ . '/employee.route.php';
// require __DIR__ . '/position.route.php';
require __DIR__ . '/approval.route.php';
require __DIR__ . '/employee.route.php';
