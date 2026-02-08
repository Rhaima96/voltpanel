<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $guarded = [];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model', \App\Models\User::class),
            'role_user'
        );
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []) || in_array('*', $this->permissions ?? []);
    }

    public function givePermissionTo(string|array $permissions): self
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        $this->permissions = array_unique(array_merge($this->permissions ?? [], $permissions));
        $this->save();

        return $this;
    }

    public function revokePermissionTo(string|array $permissions): self
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        $this->permissions = array_diff($this->permissions ?? [], $permissions);
        $this->save();

        return $this;
    }
}
