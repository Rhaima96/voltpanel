<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.multi_tenancy.tenants_table', 'tenants'));
    }

    public function users(): HasMany
    {
        $pivotTable = config('voltpanel.multi_tenancy.tenant_user_table', 'tenant_user');

        return $this->belongsToMany(
            config('auth.providers.users.model', \App\Models\User::class),
            $pivotTable
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }
}
