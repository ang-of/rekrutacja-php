<?php

namespace App\Http\Controllers;

use App\Http\Services\CommentService;
use App\Models\Post;
use App\Models\Todo;
use Illuminate\Http\Request;

class FeedController extends Controller
{

    const TAKE = 20;

    public function get()
    {
        $friend_ids = [];
        if (auth()->check()) {
            $friend_ids = user()->follows()
                ->wherePivot('status', 1)
                ->pluck('friend_id')
                ->toArray();
        }
            
        $posts = Post::when(auth()->check(), function($q) use ($friend_ids) {
                $q->where(function($q) use ($friend_ids) {
                    $q->where('user_id', user()->id);
                    $q->orWhere(function($q) use ($friend_ids) {
                        $q->whereIn('user_id', $friend_ids);
                        $q->where('status', 1);
                    });
                });
            }, function($q) {
                $q->where('status', 2);
            })
            ->with(['user', 'comments.user', 'reactions.user'])
            ->latest()
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'type' => 'post',
                    'user_id' => $post->user_id,
                    'user' => $post->user,
                    'content' => $post->content,
                    'status' => $post->status,
                    'comments' => CommentService::parse($post),
                    'reactions' => $post->formattedReactions(),
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });
        

        $feed = $posts->sortByDesc('created_at')->values();
        
        return response()->json([
            'feed' => $feed
        ], 200);
    }
}
