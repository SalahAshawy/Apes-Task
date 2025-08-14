<?php
namespace App\TenantFinder;

use Spatie\Multitenancy\TenantFinder\TenantFinder;
use App\Models\Tenant;

class ByUserTenant extends TenantFinder
{
    public function findForRequest($request): ?Tenant
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            return Tenant::find(auth()->user()->tenant_id);
        }
        return null;
    }   
}
