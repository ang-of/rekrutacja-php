<?php

namespace App\Http\Services;

use App\Models\Comment;

class CommentService {
    /**
     * Parse comments recursively with reactions
     * 
     * @param mixed $model - The model to get comments from (Post, Todo, Task, User, or Comment)
     * @return array
     */
    public static function parse($model) {
        // Get all top-level comments for the model
        $comments = $model->comments()
            ->with(['user', 'reactions.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $comments->map(function ($comment) {
            return self::parseComment($comment);
        })->toArray();
    }

    /**
     * Parse a single comment recursively
     * 
     * @param Comment $comment
     * @return array
     */
    private static function parseComment(Comment $comment): array {
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
            ],
            'reactions' => $comment->formattedReactions(),
            'comments' => self::parse($comment),
            'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            // 'updated_at' => $comment->updated_at->toIso8601String(),
        ];
    }

    private static function parseReplies(Comment $comment): array {
        $replies = $comment->comments()
            ->with(['user', 'reactions.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return $replies->map(function ($reply) {
            return self::parseComment($reply); // Recursive call
        })->toArray();
    }
}

?>