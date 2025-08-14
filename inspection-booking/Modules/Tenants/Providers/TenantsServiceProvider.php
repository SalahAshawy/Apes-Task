<?php

namespace Modules\Tenants\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Tenants\app\Http\Middleware\IdentifyTenant;

class TenantsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();

        // Register middleware alias for this module
        $router = $this->app['router'];
        $router->aliasMiddleware('tenant.identify', IdentifyTenant::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected function registerRoutes()
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(module_path('Tenants', '/Routes/api.php'));
    }
}
