<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Org\UserController;

Route::delete('/user/{userId}/document/{documentId}', [UserController::class, 'deleteUserDocument']);
