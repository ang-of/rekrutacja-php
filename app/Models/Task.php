<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, HasReactions;

    protected $fillable = [
        'todo_id',
        'parent_id',
        'task',
        'status',
    ];

    /**
     * Get the todo that owns the task
     */
    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }

    /**
     * Get the parent task
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get all child tasks
     */
    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Get all child tasks recursively
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Get all comments for the task
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


}
