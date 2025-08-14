<?php
use Illuminate\Support\Facades\Route;
use Modules\Availability\app\Http\Controllers\AvailabilityController;

Route::middleware(['auth:sanctum', 'tenant.identify'])->prefix('v1')->group(function () {
    Route::get('teams/{team}/availability', [AvailabilityController::class, 'index']);
    Route::post('teams/{team}/availability', [AvailabilityController::class, 'store']);
});
