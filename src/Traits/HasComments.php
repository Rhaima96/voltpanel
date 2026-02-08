<?php

namespace Rhaima\VoltPanel\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rhaima\VoltPanel\Models\Comment;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function comment(string $content, ?int $userId = null): Comment
    {
        return $this->comments()->create([
            'user_id' => $userId ?? auth()->id(),
            'content' => $content,
        ]);
    }

    public function getCommentsCount(): int
    {
        return $this->comments()->count();
    }
}
