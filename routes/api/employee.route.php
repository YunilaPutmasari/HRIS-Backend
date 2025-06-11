<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Org\EmployeeController;

Route::group([
    'prefix' => 'employee',
    'as' => 'employee.',
    'middleware' => 'auth:sanctum'
], function(){
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    Route::post('/import', [EmployeeController::class, 'import']);
    Route::post('/{id}/upload', [EmployeeController::class, 'upload']);
    // Employee dashboard data
    Route::get('/dashboard', [EmployeeController::class, 'getEmployeeDashboard']);
    Route::get('/profile', [EmployeeController::class, 'getEmployeeProfile']);
    Route::get('/attendance', [EmployeeController::class, 'getEmployeeAttendance']);
    Route::get('/payroll', [EmployeeController::class, 'getEmployeePayroll']);
});
