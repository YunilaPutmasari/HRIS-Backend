<?php

use App\Http\Controllers\Lettering\ApprovalController;

Route::group([
    'prefix' => 'approvals',
    'as' => 'approvals.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', [ApprovalController::class, 'index'])->name('index');
    Route::get('/create', [ApprovalController::class, 'create'])->name('create');
    Route::post('/', [ApprovalController::class, 'store'])->name('store');
    Route::get('/{id}', [ApprovalController::class, 'show'])->name('show');
    Route::put('/{id}', [ApprovalController::class, 'update'])->name('update');
    Route::delete('/{id}', [ApprovalController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/approve', [ApprovalController::class, 'approve'])->name('approve');
    Route::patch('/{id}/reject', [ApprovalController::class, 'reject'])->name('reject');
});
