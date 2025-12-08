<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, HasReactions;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (is_null($post->status)) {
                $post->status = 0;
            }
        });
    }

    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? request()->user()->id;
        
        return $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId);
            $q->orWhere(function($q) use ($userId) {
                $q->whereHas('user.followers', function($q) use ($userId) {
                    $q->where('user_user.user_id', $userId)
                    ->where('user_user.status', 1);
                });
            });
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all comments for the post
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


}
