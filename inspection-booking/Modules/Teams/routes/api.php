<?php   
use Illuminate\Support\Facades\Route;
use Modules\Teams\app\Http\Controllers\TeamController;

Route::middleware(['auth:sanctum', 'tenant.identify'])->prefix('v1')->group(function () {
    Route::get('teams', [TeamController::class, 'index']);
    Route::post('teams', [TeamController::class, 'store']);
    Route::get('teams/{id}', [TeamController::class, 'show']);
});
