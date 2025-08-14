<?php

use Illuminate\Support\Facades\Route;
use Modules\Bookings\Http\Controllers\BookingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('bookings', BookingsController::class)->names('bookings');
});
