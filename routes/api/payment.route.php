<?php

use App\Http\Controllers\Payment\PaymentController;

Route::group([
    'prefix' => 'payments',
    'as' => 'payments.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
    Route::put('/{id}', [PaymentController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
});