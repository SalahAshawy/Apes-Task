<?php

namespace Modules\Tenants\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenants\Entities\Tenant;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                app()->instance('currentTenant', $tenant);
            }
        }

        return $next($request);
    }
}
