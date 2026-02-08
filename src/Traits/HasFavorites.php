<?php

namespace Rhaima\VoltPanel\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rhaima\VoltPanel\Models\Favorite;

trait HasFavorites
{
    public function favorites(): MorphToMany
    {
        return $this->morphToMany(
            get_class($this),
            'favoritable',
            'favorites',
            'user_id',
            'favoritable_id'
        )->withTimestamps();
    }

    public function favorite($model): bool
    {
        return app(\Rhaima\VoltPanel\Favorites\FavoriteManager::class)
            ->toggle(get_class($model), $model->id, $this->id);
    }

    public function unfavorite($model): bool
    {
        return !$this->favorite($model);
    }

    public function hasFavorited($model): bool
    {
        return app(\Rhaima\VoltPanel\Favorites\FavoriteManager::class)
            ->isFavorited(get_class($model), $model->id, $this->id);
    }
}
