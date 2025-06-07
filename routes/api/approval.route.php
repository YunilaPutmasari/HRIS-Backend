<?php

use App\Http\Controllers\Lettering\ApprovalController;

Route::group([
    'prefix' => 'approvals',
    'as' => 'approvals.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', [ApprovalController::class, 'index'])->name('index');
    Route::post('/', [ApprovalController::class, 'store'])->name('store');
    Route::get('/{id}', [ApprovalController::class, 'show'])->name('show');
    Route::put('/{id}', [ApprovalController::class, 'update'])->name('update');
    Route::delete('/{id}', [ApprovalController::class, 'destroy'])->name('destroy');
});
