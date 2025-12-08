<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory, HasReactions;

    protected $guarded = [];

    /**
     * Get the user that owns the todo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all tasks for the todo
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all comments for the todo
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


}
