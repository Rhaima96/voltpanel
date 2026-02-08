<?php

namespace Rhaima\VoltPanel\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rhaima\VoltPanel\Models\Tag;
use Illuminate\Support\Collection;

trait HasTags
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            Tag::class,
            'taggable',
            'taggables'
        );
    }

    public function tag(string|array $tags): void
    {
        $tagIds = collect($tags)->map(function ($tag) {
            if ($tag instanceof Tag) {
                return $tag->id;
            }

            return app(\Rhaima\VoltPanel\Tags\TagManager::class)
                ->findOrCreate($tag)
                ->id;
        });

        $this->tags()->syncWithoutDetaching($tagIds);
    }

    public function untag(string|array $tags): void
    {
        $tagIds = collect($tags)->map(function ($tag) {
            if ($tag instanceof Tag) {
                return $tag->id;
            }

            $tagModel = Tag::where('slug', str($tag)->slug())->first();

            return $tagModel?->id;
        })->filter();

        $this->tags()->detach($tagIds);
    }

    public function hasTag(string $tag): bool
    {
        return $this->tags()
            ->where('slug', str($tag)->slug())
            ->exists();
    }

    public function getTagNames(): Collection
    {
        return $this->tags->pluck('name');
    }
}
