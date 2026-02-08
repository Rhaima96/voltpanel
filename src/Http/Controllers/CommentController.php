<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rhaima\VoltPanel\Comments\CommentManager;

class CommentController extends Controller
{
    public function __construct(
        protected CommentManager $commentManager
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'content' => 'required|string|max:2000',
        ]);

        $comment = $this->commentManager->create(
            $validated['commentable_type'],
            $validated['commentable_id'],
            $validated['content']
        );

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(int $id)
    {
        $deleted = $this->commentManager->delete($id);

        if (!$deleted) {
            return response()->json(['error' => 'Comment not found or unauthorized'], 404);
        }

        return response()->json(['success' => true]);
    }
}
