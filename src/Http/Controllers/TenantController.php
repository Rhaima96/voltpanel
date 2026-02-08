<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rhaima\VoltPanel\Tenancy\TenantManager;

class TenantController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function index()
    {
        $tenants = $this->tenantManager->getUserTenants();
        $currentTenant = $this->tenantManager->getCurrentTenant();

        return response()->json([
            'tenants' => $tenants,
            'current' => $currentTenant,
        ]);
    }

    public function switch(Request $request, int $tenantId)
    {
        $success = $this->tenantManager->switchTenant($tenantId);

        if (!$success) {
            return response()->json(['error' => 'Unable to switch tenant'], 403);
        }

        return response()->json([
            'success' => true,
            'tenant' => $this->tenantManager->getCurrentTenant(),
        ]);
    }

    public function current()
    {
        return response()->json([
            'tenant' => $this->tenantManager->getCurrentTenant(),
        ]);
    }
}
