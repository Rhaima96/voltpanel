<?php

namespace Rhaima\VoltPanel\Favorites;

use Rhaima\VoltPanel\Models\Favorite;

class FavoriteManager
{
    public function toggle(string $favoritableType, int $favoritableId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        $favorite = Favorite::where('user_id', $userId)
            ->where('favoritable_type', $favoritableType)
            ->where('favoritable_id', $favoritableId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        Favorite::create([
            'user_id' => $userId,
            'favoritable_type' => $favoritableType,
            'favoritable_id' => $favoritableId,
        ]);

        return true;
    }

    public function isFavorited(string $favoritableType, int $favoritableId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', $favoritableType)
            ->where('favoritable_id', $favoritableId)
            ->exists();
    }

    public function getFavorites(?int $userId = null, ?string $type = null)
    {
        $userId = $userId ?? auth()->id();

        $query = Favorite::where('user_id', $userId)->with('favoritable');

        if ($type) {
            $query->where('favoritable_type', $type);
        }

        return $query->latest()->get();
    }
}
