<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenants\Http\Controllers\TenantsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tenants', TenantsController::class)->names('tenants');
});
