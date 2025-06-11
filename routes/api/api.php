<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Controllers\org\EmployeeController;
use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\Org\EmployeeController;
use App\Http\Controllers\Payment\XenditWebhookController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('', function (Request $request) {
    return UserResource::collection(User::all());
});


Route::post('/xendit/webhook/invoice', [XenditWebhookController::class, 'handle']);

require __DIR__ . '/auth.route.php';
require __DIR__ . '/admin.route.php';
require __DIR__ . '/user.route.php';
require __DIR__ . '/employee.route.php';
require __DIR__ . '/position.route.php';
require __DIR__ . '/approval.route.php';
