<?php

namespace Rhaima\VoltPanel\Authorization\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('voltpanel.authorization.role_model', \Rhaima\VoltPanel\Models\Role::class),
            config('voltpanel.authorization.role_user_table', 'role_user')
        );
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('name', $roles)->exists();
        }

        return $this->roles()->where('name', $roles)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->count() === count($roles);
    }

    public function assignRole(string|array $roles): self
    {
        $roles = is_array($roles) ? $roles : [$roles];

        foreach ($roles as $role) {
            $roleModel = config('voltpanel.authorization.role_model')::firstOrCreate(['name' => $role]);
            $this->roles()->syncWithoutDetaching($roleModel);
        }

        return $this;
    }

    public function removeRole(string|array $roles): self
    {
        $roles = is_array($roles) ? $roles : [$roles];

        $roleModels = config('voltpanel.authorization.role_model')::whereIn('name', $roles)->get();
        $this->roles()->detach($roleModels);

        return $this;
    }

    public function syncRoles(array $roles): self
    {
        $roleModels = config('voltpanel.authorization.role_model')::whereIn('name', $roles)->get();
        $this->roles()->sync($roleModels);

        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles->contains(function ($role) use ($permission) {
            return in_array($permission, $role->permissions) || in_array('*', $role->permissions);
        });
    }
}
