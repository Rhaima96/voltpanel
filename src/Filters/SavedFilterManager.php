<?php

namespace Rhaima\VoltPanel\Filters;

use Rhaima\VoltPanel\Models\SavedFilter;
use Illuminate\Support\Collection;

class SavedFilterManager
{
    public function getSavedFilters(string $resource, ?int $userId = null): Collection
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return collect();
        }

        return SavedFilter::getForUser($userId, $resource);
    }

    public function saveFilter(
        string $resource,
        string $name,
        array $filters,
        bool $isPublic = false,
        bool $isDefault = false,
        ?int $userId = null
    ): SavedFilter {
        $userId = $userId ?? auth()->id();

        return SavedFilter::saveFilter([
            'user_id' => $userId,
            'resource' => $resource,
            'name' => $name,
            'filters' => $filters,
            'is_public' => $isPublic,
            'is_default' => $isDefault,
        ]);
    }

    public function deleteFilter(int $filterId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return SavedFilter::where('id', $filterId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    public function makeDefault(int $filterId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        $filter = SavedFilter::where('id', $filterId)
            ->where('user_id', $userId)
            ->first();

        if (!$filter) {
            return false;
        }

        $filter->makeDefault();

        return true;
    }

    public function getDefaultFilter(string $resource, ?int $userId = null): ?SavedFilter
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return null;
        }

        return SavedFilter::forUser($userId)
            ->forResource($resource)
            ->default()
            ->first();
    }
}
