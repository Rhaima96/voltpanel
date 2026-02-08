<?php

namespace Rhaima\VoltPanel\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rhaima\VoltPanel\Models\Favorite;

trait CanBeFavorited
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavoritedBy(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return $this->favorites()
            ->where('user_id', $userId)
            ->exists();
    }

    public function favoritedByCount(): int
    {
        return $this->favorites()->count();
    }
}
