<?php use


App\Http\Controllers\EmployeeController;

Route::group([
    'prefix' => 'employee',
    'as' => 'employee.',
    'middleware' => 'auth:sanctum' // agar hanya user yang login bisa akses
], function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index'); // ✅ List all employees
    Route::post('/', [EmployeeController::class, 'store'])->name('store'); // ✅ Create new employee
    Route::get('/{id}', [EmployeeController::class, 'show'])->name('show'); // ✅ Show employee by ID
    Route::put('/{id}', [EmployeeController::class, 'update'])->name('update'); // ✅ Update employee by ID
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy'); // ✅ Soft delete employee by ID
});