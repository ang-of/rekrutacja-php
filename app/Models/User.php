<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasReactions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all todos for the user
     */
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'friend_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_user', 'friend_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get all comments made by the user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all comments on the user's profile
     */
    public function profileComments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all reactions made by the user
     */
    public function userReactions()
    {
        return $this->hasMany(Reaction::class);
    }


}
