<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Http\Resources\EmployeeResource;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('', function (Request $request) {
    return UserResource::collection(User::all());
});
// Route::get('/employee', function () {
//     return EmployeeResource::collection(Employee::all());
// });
Route::get('/employee', function () {
    $employees = Employee::with('position', 'user')->get();  // eager load relasi position dan user
    return EmployeeResource::collection($employees);
});

require __DIR__ . '/auth.route.php';
require __DIR__ . '/admin.route.php';