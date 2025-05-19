<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployeeController;

Route::group([
    'prefix' => 'auth',
    'as' => 'auth.',
], function () {
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('/signin', [AuthController::class, 'signin'])->name('signin');
    Route::get('/me', [AuthController::class, 'me'])->name('me')->middleware('auth:sanctum');
});
