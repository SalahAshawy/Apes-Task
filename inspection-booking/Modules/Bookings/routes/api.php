<?php

use Modules\Bookings\app\Http\Controllers\BookingController;
use Modules\Teams\app\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'tenant.identify'])->prefix('v1')->group(function () {
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::delete('bookings/{id}', [BookingController::class, 'destroy']);

    Route::get('teams/{id}/generate-slots', [TeamController::class, 'generateSlots']);
});

