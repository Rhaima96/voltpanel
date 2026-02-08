<?php

namespace Rhaima\VoltPanel\Tenancy;

use Rhaima\VoltPanel\Models\Tenant;
use Illuminate\Support\Facades\Cache;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    public function setCurrentTenant(?Tenant $tenant): void
    {
        $this->currentTenant = $tenant;

        if ($tenant) {
            Cache::put('voltpanel.current_tenant_id', $tenant->id, now()->addHours(2));
        } else {
            Cache::forget('voltpanel.current_tenant_id');
        }
    }

    public function getCurrentTenant(): ?Tenant
    {
        if ($this->currentTenant) {
            return $this->currentTenant;
        }

        $tenantId = Cache::get('voltpanel.current_tenant_id');

        if ($tenantId) {
            $this->currentTenant = Tenant::find($tenantId);
        }

        return $this->currentTenant;
    }

    public function getCurrentTenantId(): ?int
    {
        return $this->getCurrentTenant()?->id;
    }

    public function isTenantActive(): bool
    {
        return $this->getCurrentTenant() !== null;
    }

    public function switchTenant(int $tenantId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant || !$tenant->is_active) {
            return false;
        }

        // Check if user has access to this tenant
        if (!$this->userHasAccessToTenant($userId, $tenantId)) {
            return false;
        }

        $this->setCurrentTenant($tenant);

        return true;
    }

    public function userHasAccessToTenant(int $userId, int $tenantId): bool
    {
        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::find($userId);

        if (!$user) {
            return false;
        }

        // Check if user belongs to tenant
        return $user->tenants()->where('tenant_id', $tenantId)->exists();
    }

    public function getUserTenants(?int $userId = null): \Illuminate\Support\Collection
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return collect();
        }

        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::find($userId);

        if (!$user) {
            return collect();
        }

        return $user->tenants;
    }

    public function forgetCurrentTenant(): void
    {
        $this->setCurrentTenant(null);
    }
}
