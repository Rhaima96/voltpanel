<?php

namespace Rhaima\VoltPanel\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rhaima\VoltPanel\Tenancy\TenantManager;

class SetTenantFromRequest
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function handle(Request $request, Closure $next)
    {
        // Try to get tenant from subdomain
        if (config('voltpanel.multi_tenancy.identify_by_subdomain')) {
            $subdomain = $this->getSubdomain($request);

            if ($subdomain) {
                $tenant = \Rhaima\VoltPanel\Models\Tenant::where('subdomain', $subdomain)->first();

                if ($tenant && $tenant->is_active) {
                    $this->tenantManager->setCurrentTenant($tenant);
                    return $next($request);
                }
            }
        }

        // Try to get tenant from domain
        if (config('voltpanel.multi_tenancy.identify_by_domain')) {
            $domain = $request->getHost();
            $tenant = \Rhaima\VoltPanel\Models\Tenant::where('domain', $domain)->first();

            if ($tenant && $tenant->is_active) {
                $this->tenantManager->setCurrentTenant($tenant);
                return $next($request);
            }
        }

        // Try to get tenant from session/cache (manual switching)
        $currentTenant = $this->tenantManager->getCurrentTenant();

        if (!$currentTenant && auth()->check()) {
            // Auto-select first available tenant for user
            $tenants = $this->tenantManager->getUserTenants();

            if ($tenants->isNotEmpty()) {
                $this->tenantManager->setCurrentTenant($tenants->first());
            }
        }

        return $next($request);
    }

    protected function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }
}
