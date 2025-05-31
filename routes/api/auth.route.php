<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ForgotPasswordController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/signup', [AuthController::class, 'signup'])->name('auth.signup');
    Route::post('/signin', [AuthController::class, 'signin'])->name('auth.signin');

    Route::get('/me', [AuthController::class, 'me'])->name('auth.me')->middleware('auth:sanctum');

    Route::get('/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/google/callback', [AuthController::class, 'redirectToGoogleCallback'])->name('auth.google.callback');
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->name('password.update');
