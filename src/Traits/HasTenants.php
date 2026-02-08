<?php

namespace Rhaima\VoltPanel\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rhaima\VoltPanel\Models\Tenant;

trait HasTenants
{
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            config('voltpanel.multi_tenancy.tenant_user_table', 'tenant_user')
        )->withPivot('role')->withTimestamps();
    }

    public function addToTenant(int|Tenant $tenant, ?string $role = null): void
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        $this->tenants()->attach($tenantId, ['role' => $role]);
    }

    public function removeFromTenant(int|Tenant $tenant): void
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        $this->tenants()->detach($tenantId);
    }

    public function belongsToTenant(int|Tenant $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->tenants()->where('tenant_id', $tenantId)->exists();
    }
}
