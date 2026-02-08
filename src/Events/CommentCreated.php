<?php

namespace Rhaima\VoltPanel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rhaima\VoltPanel\Models\Comment;

class CommentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment instance.
     */
    public Comment $comment;

    /**
     * The mentioned user names extracted from the comment.
     */
    public array $mentions;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment, array $mentions = [])
    {
        $this->comment = $comment;
        $this->mentions = $mentions;
    }
}
