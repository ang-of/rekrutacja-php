<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    private const REACTABLE_TYPES = [
        'user' => User::class,
        'post' => Post::class,
        'todo' => Todo::class,
        'task' => Task::class,
        'comment' => Comment::class,
    ];

    public function __invoke(Request $request, $commentable_type, $commentable_id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        // Sprawdź czy komentowany zasób istnieje
        $commentableClass = 'App\\Models\\' . $commentable_type;
        $commentable = $commentableClass::find($commentable_id);
        
        if (!$commentable) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $comment = Comment::create([
            'user_id' => user()->id,
            'content' => $request->comment,
            'commentable_type' => $commentableClass,
            'commentable_id' => $commentable_id
        ]);

        $comment->load('user:id,name');

        return response()->json($comment, 201);
    }
}
