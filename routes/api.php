<?php

use App\Enums\Apartment\Governorate;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentPhotoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationModificationController;
use App\Http\Controllers\ReservationRequestController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::post('apartment/{apartmentId}/photo', [ApartmentPhotoController::class, 'store']);
Route::get('apartment/{apartmentId}/photo', [ApartmentPhotoController::class, 'index']);
Route::put('apartment/{apartmentId}/photo/{photoId}/main', [ApartmentPhotoController::class, 'setMainPhoto']);
Route::get('apartment/{apartmentId}/photo/main', [ApartmentPhotoController::class, 'getMainPhoto']);

Route::post('apartment/{id}/favorite', [FavoriteController::class, 'store']);
Route::delete('apartment/{id}/favorite', [FavoriteController::class, 'destroy']);
Route::get('favorites', [FavoriteController::class, 'index']);

Route::apiResource('apartment/review', ReviewController::class);

Route::apiResource('apartment', ApartmentController::class);

Route::apiResource('media', MediumController::class)->only(['show', 'store']);
Route::post('media/store-many', [MediumController::class, 'storeMultiple']);

Route::get('governorates', function () {
    return Governorate::allLabels();
});
Route::get('notifications', [NotificationController::class, 'index']);
Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
Route::post('notifications/read', [NotificationController::class, 'markAsRead']);

// for admin dashboard
Route::get('system-data', [SystemController::class, 'getData']);
Route::apiResource('users', UserController::class);

Route::apiResource('reservation-requests', ReservationRequestController::class);

Route::apiResource('reservations', ReservationController::class);
Route::post('reservation-requests/{id}/accept', [ReservationController::class, 'accept']);
Route::post('reservation-requests/{id}/reject', [ReservationController::class, 'reject']);

Route::apiResource('reservation-modifications', ReservationModificationController::class);
Route::post('reservations/{id}/modifications', [ReservationModificationController::class, 'requestModification']);
Route::post('modifications/{id}/accept', [ReservationModificationController::class, 'acceptModification']);
Route::post('modifications/{id}/reject', [ReservationModificationController::class, 'rejectModification']);
