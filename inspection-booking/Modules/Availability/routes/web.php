<?php

use Illuminate\Support\Facades\Route;
use Modules\Availability\Http\Controllers\AvailabilityController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('availabilities', AvailabilityController::class)->names('availability');
});
