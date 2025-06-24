<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Lettering\LetterEmployeeController;

Route::get('/letter', [LetterEmployeeController::class, 'index']);
Route::get('/letter/{id}/download-pdf', [LetterEmployeeController::class, 'downloadPdf']);

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::group([
        'prefix' => 'dashboard',
        'as' => 'dashboard.',
    ], function () {
        Route::get('/daily-attendance',[DashboardController::class, 'getEmployeeData'])->name('getEmployeeData');
    });
});