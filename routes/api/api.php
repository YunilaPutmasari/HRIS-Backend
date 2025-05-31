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



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('', function (Request $request) {
    return UserResource::collection(User::all());
});
// Route::get('/employee', function () {
//     return EmployeeResource::collection(Employee::all());
// });



// Route::get('/employee', function () {
//     $employees = Employee::with('position', 'user')->get();  // eager load relasi position dan user
//     return EmployeeResource::collection($employees);
// });



require __DIR__ . '/auth.route.php';
require __DIR__ . '/employee.route.php';

Route::get('/employee', function () {
    $employees = Employee::with('position', 'user')->get();  // eager load relasi position dan user
    return EmployeeResource::collection($employees);
});

Route::post('/xendit/webhook/invoice', [XenditWebhookController::class, 'handle']);

require __DIR__ . '/auth.route.php';
require __DIR__ . '/admin.route.php';

