<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Org\EmployeeController;

Route::group([
    'prefix' => 'employee',
    'as' => 'employee.',
    'middleware' => 'auth:sanctum'
], function(){
    // Employee dashboard data
    Route::get('/dashboard', [EmployeeController::class, 'getEmployeeDashboard']);
    Route::get('/profile', [EmployeeController::class, 'getEmployeeProfile']);
    Route::get('/attendance', [EmployeeController::class, 'getEmployeeAttendance']);
    Route::get('/payroll', [EmployeeController::class, 'getEmployeePayroll']);
});