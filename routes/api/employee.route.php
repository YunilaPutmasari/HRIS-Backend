<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\org\EmployeeController;

// Route::prefix('employee')->middleware('auth:sanctum')->group(function () {
Route::prefix('employee')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});
