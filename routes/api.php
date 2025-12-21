<?php

use App\Enums\Apartment\Governorate;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentPhotoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::post('apartment/{apartmentId}/photo', [ApartmentPhotoController::class, 'store']);
Route::get('apartment/{apartmentId}/photo', [ApartmentPhotoController::class, 'index']);
Route::put('apartment/{apartmentId}/photo/{photoId}/main', [ApartmentPhotoController::class, 'setMainPhoto']);
Route::get('apartment/{apartmentId}/photo/main', [ApartmentPhotoController::class, 'getMainPhoto']);

Route::post('apartment/{id}/favorite', [FavoriteController::class, 'store']);
Route::delete('apartment/{id}/favorite', [FavoriteController::class, 'destroy']);
Route::get('favorites', [FavoriteController::class, 'index']);

Route::apiResource('apartment/{apartmentId}/photo', ApartmentPhotoController::class);

Route::apiResource('apartment/review', ReviewController::class);

Route::apiResource('apartment', ApartmentController::class);

Route::apiResource('media', MediumController::class)->only(['show', 'store']);
Route::post('media/store-many', [MediumController::class, 'storeMultiple']);

Route::get('governorates', function () {
    return Governorate::allLabels();
});
Route::apiResource('users', UserController::class);
