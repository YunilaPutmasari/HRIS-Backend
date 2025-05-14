<?php

use App\Http\Resources\UserResource;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('', function (Request $request) {
    return UserResource::collection(User::all());
});

// Route untuk Payment
Route::apiResource('payments', PaymentController::class);

require __DIR__ . '/auth.route.php';