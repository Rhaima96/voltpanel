<?php

namespace Rhaima\VoltPanel\Comments;

use Rhaima\VoltPanel\Events\CommentCreated;
use Rhaima\VoltPanel\Models\Comment;

class CommentManager
{
    public function create(string $commentableType, int $commentableId, string $content, ?int $userId = null): Comment
    {
        $comment = Comment::create([
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'user_id' => $userId ?? auth()->id(),
            'content' => $content,
        ]);

        $mentions = $this->extractMentions($content);

        event(new CommentCreated($comment, $mentions));

        return $comment;
    }

    public function getComments(string $commentableType, int $commentableId)
    {
        return Comment::where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function delete(int $commentId, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return Comment::where('id', $commentId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    public function extractMentions(string $content): array
    {
        preg_match_all('/@(\w+)/', $content, $matches);

        return $matches[1] ?? [];
    }
}
