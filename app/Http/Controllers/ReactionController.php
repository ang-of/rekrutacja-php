<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\User;
use App\Models\Post;
use App\Models\Todo;
use App\Models\Task;
use App\Models\Comment;
use App\Rules\EmojiOnly;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    private const REACTABLE_TYPES = [
        'user' => User::class,
        'post' => Post::class,
        'todo' => Todo::class,
        'task' => Task::class,
        'comment' => Comment::class,
    ];

    public function __invoke(Request $request, $reactable_type, $reactable_id)
    {
        $request->validate([
            'emoji' => ['required', 'string', 'max:10', new EmojiOnly],
        ]);

        // Pobierz klasę modelu ze słownika
        $class = self::REACTABLE_TYPES[$reactable_type];
        $reactable = $class::find($reactable_id);
        
        if (!$reactable) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Znajdź istniejącą reakcję użytkownika dla tego zasobu
        $existing_reaction = Reaction::where('user_id', user()->id)
            ->where('reactable_type', $class)
            ->where('reactable_id', $reactable_id)
            ->first();

        // Jeśli reakcja istnieje
        if ($existing_reaction) {
            if ($existing_reaction->emoji === $request->emoji) {
                $existing_reaction->delete();
                return response()->json(['message' => 'Reaction removed'], 200);
            }
            
            // Jeśli to inna reakcja - zaktualizuj
            $existing_reaction->update(['emoji' => $request->emoji]);
            $existing_reaction->load('user:id,name');
            return response()->json($existing_reaction, 200);
        }

        // Jeśli reakcji nie ma - utwórz nową
        $reaction = Reaction::create([
            'user_id' => user()->id,
            'emoji' => $request->emoji,
            'reactable_type' => $class,
            'reactable_id' => $reactable_id
        ]);

        $reaction->load('user:id,name');

        return response()->json($reaction, 201);
    }
}
