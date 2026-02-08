<?php

namespace Rhaima\VoltPanel\Authorization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class ResourcePolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Model $model): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Model $model): bool
    {
        return true;
    }

    public function delete($user, Model $model): bool
    {
        return true;
    }

    public function restore($user, Model $model): bool
    {
        return true;
    }

    public function forceDelete($user, Model $model): bool
    {
        return true;
    }

    public function replicate($user, Model $model): bool
    {
        return true;
    }

    public function reorder($user): bool
    {
        return true;
    }

    public function attachAny($user): bool
    {
        return true;
    }

    public function attach($user, Model $model, Model $relatedModel): bool
    {
        return true;
    }

    public function detach($user, Model $model, Model $relatedModel): bool
    {
        return true;
    }

    public function detachAny($user, Model $model): bool
    {
        return true;
    }
}
