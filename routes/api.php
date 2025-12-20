<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::apiResource('media', MediumController::class)->only(['show', 'store']);
Route::post('media/store-many', [MediumController::class, 'storeMultiple']);
Route::apiResource('users', UserController::class);