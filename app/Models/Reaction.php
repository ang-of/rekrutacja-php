<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'emoji', 'reactable_id', 'reactable_type'];

    /**
     * Get the user that owns the reaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reactable model (Post, Comment, Todo, Task, User)
     */
    public function reactable()
    {
        return $this->morphTo();
    }
}
