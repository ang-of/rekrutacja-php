<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('feed', [FeedController::class, 'get'])->middleware('auth.optional');

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(UserController::class)->group(function() {
        Route::get('user', 'user');
        Route::get('users', 'users');
        Route::get('invites', 'invites');

        Route::get('friends', 'friends');
        
        Route::put('invites/{id}', 'accept');
        Route::delete('invites/{id}', 'decline');

        Route::put('users/{id}/follow', 'follow');
        Route::delete('users/{id}/unfollow', 'unfollow');
        
        Route::get('users/{id}/todos', 'todos');
    });

    Route::post('posts/{id?}', [PostController::class, 'post']);
    Route::delete('posts/{id}', [PostController::class, 'delete']);

    Route::post('reaction/{model}/{id}', ReactionController::class);
    Route::post('comment/{model}/{id}', CommentController::class);

    Route::apiResource('todos', TodoController::class);
    Route::apiResource('todos.tasks', TaskController::class)->parameters([
        'todos' => 'todoId'
    ]);
    
    Route::post('logout', [AuthController::class, 'logout']);

});

