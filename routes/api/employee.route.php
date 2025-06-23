<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Lettering\LetterEmployeeController;

Route::get('/letter', [LetterEmployeeController::class, 'index']);
Route::get('/letter/{id}/download-pdf', [LetterEmployeeController::class, 'downloadPdf']);
// Route::group([
//     'prefix' => 'employee',
//     'as' => 'employee.',
// ], function () {
//     Route::get('/dashboard', [EmployeeController::class, 'getEmployeeDashboard']);
//     Route::get('/profile', [EmployeeController::class, 'getEmployeeProfile']);
//     Route::get('/attendance', [EmployeeController::class, 'getEmployeeAttendance']);
//     Route::get('/payroll', [EmployeeController::class, 'getEmployeePayroll']);
// });
