<?php

namespace Rhaima\VoltPanel\Tenancy\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rhaima\VoltPanel\Models\Tenant;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->bound('voltpanel.tenancy')) {
                $tenantId = app('voltpanel.tenancy')->getCurrentTenantId();

                if ($tenantId) {
                    $builder->where(static::getTenantColumnName(), $tenantId);
                }
            }
        });

        static::creating(function ($model) {
            if (app()->bound('voltpanel.tenancy')) {
                $tenantId = app('voltpanel.tenancy')->getCurrentTenantId();

                if ($tenantId && !$model->{static::getTenantColumnName()}) {
                    $model->{static::getTenantColumnName()} = $tenantId;
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, static::getTenantColumnName());
    }

    public static function getTenantColumnName(): string
    {
        return 'tenant_id';
    }

    public function scopeAllTenants(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }

    public function scopeForTenant(Builder $query, int|Tenant $tenant): Builder
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->withoutGlobalScope('tenant')
            ->where(static::getTenantColumnName(), $tenantId);
    }
}
