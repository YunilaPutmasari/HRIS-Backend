<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lettering\LetterEmployeeController;

Route::middleware(['auth:sanctum'])->get('/letter', [LetterEmployeeController::class, 'index']);

// Route::get('/letter', [LetterEmployeeController::class, 'index']);
Route::get('/letter/{id}/download-pdf', [LetterEmployeeController::class, 'downloadPdf']);
