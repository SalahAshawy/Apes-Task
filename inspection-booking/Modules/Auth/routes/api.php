<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\app\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes: only authenticated users + tenant scoped
    Route::middleware(['auth:sanctum', 'tenant.identify'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Example: tenant-only endpoint
        Route::get('tenant/me', function () {
            return response()->json(app()->make('currentTenant'));
        });
    });
});
