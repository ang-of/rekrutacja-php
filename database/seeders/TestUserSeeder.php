<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;
use App\Models\Todo;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Reaction;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.pl',
            'password' => Hash::make('test1234'),
        ]);

        // Create second user for interactions
        $user2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.pl',
            'password' => Hash::make('test1234'),
        ]);

        // Create 2 posts
        $post1 = Post::create([
            'user_id' => $user->id,
            'content' => 'This is my first test post. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'status' => 1,
        ]);

        $post2 = Post::create([
            'user_id' => $user->id,
            'content' => 'This is my second test post. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'status' => 1,
        ]);

        // Add comments to posts
        $comment1 = Comment::create([
            'user_id' => $user2->id,
            'content' => 'Great post! Really enjoyed reading this.',
            'commentable_id' => $post1->id,
            'commentable_type' => Post::class,
        ]);

        $comment2 = Comment::create([
            'user_id' => $user->id,
            'content' => 'Thanks for reading!',
            'commentable_id' => $post1->id,
            'commentable_type' => Post::class,
        ]);

        $comment3 = Comment::create([
            'user_id' => $user2->id,
            'content' => 'Interesting thoughts here.',
            'commentable_id' => $post2->id,
            'commentable_type' => Post::class,
        ]);

        // Add reactions to posts
        Reaction::create([
            'user_id' => $user2->id,
            'emoji' => '👍',
            'reactable_id' => $post1->id,
            'reactable_type' => Post::class,
        ]);

        Reaction::create([
            'user_id' => $user->id,
            'emoji' => '❤️',
            'reactable_id' => $post2->id,
            'reactable_type' => Post::class,
        ]);

        // Add reactions to comments
        Reaction::create([
            'user_id' => $user->id,
            'emoji' => '😊',
            'reactable_id' => $comment1->id,
            'reactable_type' => Comment::class,
        ]);

        // Create 2 todos, each with 3 tasks
        $todo1 = Todo::create([
            'user_id' => $user->id,
            'title' => 'Shopping List',
            'status' => 0,
        ]);

        $task1 = Task::create([
            'todo_id' => $todo1->id,
            'task' => 'Buy milk',
            'status' => 0,
        ]);

        $task2 = Task::create([
            'todo_id' => $todo1->id,
            'task' => 'Buy bread',
            'status' => 0,
        ]);

        $task3 = Task::create([
            'todo_id' => $todo1->id,
            'task' => 'Buy eggs',
            'status' => 1,
        ]);

        $todo2 = Todo::create([
            'user_id' => $user->id,
            'title' => 'Work Tasks',
            'status' => 0,
        ]);

        $task4 = Task::create([
            'todo_id' => $todo2->id,
            'task' => 'Finish project documentation',
            'status' => 0,
        ]);

        $task5 = Task::create([
            'todo_id' => $todo2->id,
            'task' => 'Review pull requests',
            'status' => 1,
        ]);

        $task6 = Task::create([
            'todo_id' => $todo2->id,
            'task' => 'Update dependencies',
            'status' => 0,
        ]);

        // Add comments to todos
        Comment::create([
            'user_id' => $user2->id,
            'content' => 'Don\'t forget the organic milk!',
            'commentable_id' => $todo1->id,
            'commentable_type' => Todo::class,
        ]);

        Comment::create([
            'user_id' => $user->id,
            'content' => 'This list is getting long...',
            'commentable_id' => $todo1->id,
            'commentable_type' => Todo::class,
        ]);

        // Add comments to tasks
        Comment::create([
            'user_id' => $user2->id,
            'content' => 'I can help with this one.',
            'commentable_id' => $task4->id,
            'commentable_type' => Task::class,
        ]);

        Comment::create([
            'user_id' => $user->id,
            'content' => 'Almost done with this task!',
            'commentable_id' => $task5->id,
            'commentable_type' => Task::class,
        ]);

        // Add reactions to todos
        Reaction::create([
            'user_id' => $user2->id,
            'emoji' => '📝',
            'reactable_id' => $todo1->id,
            'reactable_type' => Todo::class,
        ]);

        Reaction::create([
            'user_id' => $user->id,
            'emoji' => '💼',
            'reactable_id' => $todo2->id,
            'reactable_type' => Todo::class,
        ]);

        // Add reactions to tasks
        Reaction::create([
            'user_id' => $user2->id,
            'emoji' => '✅',
            'reactable_id' => $task3->id,
            'reactable_type' => Task::class,
        ]);

        Reaction::create([
            'user_id' => $user->id,
            'emoji' => '🔥',
            'reactable_id' => $task5->id,
            'reactable_type' => Task::class,
        ]);

        Reaction::create([
            'user_id' => $user2->id,
            'emoji' => '👀',
            'reactable_id' => $task4->id,
            'reactable_type' => Task::class,
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('User 1 - Email: test@test.pl, Password: test1234');
        $this->command->info('User 2 - Email: john@test.pl, Password: test1234');
    }
}
