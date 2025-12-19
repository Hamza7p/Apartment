<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentPhotoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediumController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::apiResource('apartment', ApartmentController::class);

Route::apiResource('apartment/{id}/photo', ApartmentPhotoController::class);
// Route::post('apartment/{id}/photo', [ApartmentPhotoController::class, 'store']);
// Route::get('apartment/{id}/photo', [ApartmentPhotoController::class, 'show']);
Route::put('apartment/{apartmentId}/photo/{photoId}/main', [ApartmentPhotoController::class, 'setMainPhoto']);

Route::apiResource('media', MediumController::class)->only(['show', 'store']);
Route::post('media/store-many', [MediumController::class, 'storeMultiple']);
