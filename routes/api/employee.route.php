<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lettering\LetterEmployeeController;

Route::get('/letter', [LetterEmployeeController::class, 'index']);