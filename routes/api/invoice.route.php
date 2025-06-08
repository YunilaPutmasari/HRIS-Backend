<?php

use App\Http\Controllers\Payment\InvoiceController;

Route::group([
    'prefix' => 'invoices',
    'as' => 'invoices.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
    Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
});