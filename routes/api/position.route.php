<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Org\PositionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('positions', PositionController::class);
});
