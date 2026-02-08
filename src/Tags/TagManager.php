<?php

namespace Rhaima\VoltPanel\Tags;

use Illuminate\Support\Collection;
use Rhaima\VoltPanel\Models\Tag;

class TagManager
{
    public function create(string $name, ?string $color = null, ?string $type = 'default'): Tag
    {
        return Tag::create([
            'name' => $name,
            'slug' => str($name)->slug(),
            'color' => $color,
            'type' => $type,
        ]);
    }

    public function findOrCreate(string $name, ?string $color = null): Tag
    {
        return Tag::firstOrCreate(
            ['slug' => str($name)->slug()],
            ['name' => $name, 'color' => $color]
        );
    }

    public function attachToModel($model, array $tagNames): void
    {
        $tags = collect($tagNames)->map(fn($name) => $this->findOrCreate($name));

        $model->tags()->sync($tags->pluck('id'));
    }

    public function getPopularTags(int $limit = 10): Collection
    {
        return Tag::withCount('taggables')
            ->orderBy('taggables_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
