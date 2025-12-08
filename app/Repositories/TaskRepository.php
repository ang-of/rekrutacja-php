<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function __construct(
        protected Task $model
    ) {}

    /**
     * Get all root tasks for a todo with recursive children
     */
    public function getAllForTodo(int $todoId): Collection
    {
        return $this->model
            ->where('todo_id', $todoId)
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();
    }

    /**
     * Find task by ID with optional recursive children
     */
    public function findById(int $id, bool $withChildren = false): ?Task
    {
        $query = $this->model->where('id', $id);
        
        if ($withChildren) {
            $query->with('childrenRecursive');
        }
        
        return $query->first();
    }

    /**
     * Find task by ID and todo ID
     */
    public function findByIdAndTodo(int $id, int $todoId, bool $withChildren = false): ?Task
    {
        $query = $this->model->where('id', $id)->where('todo_id', $todoId);
        
        if ($withChildren) {
            $query->with('childrenRecursive');
        }
        
        return $query->first();
    }

    /**
     * Create new task
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Update existing task
     */
    public function update(int $id, array $data): bool
    {
        $task = $this->findById($id);
        
        if (!$task) {
            return false;
        }

        return $task->update($data);
    }

    /**
     * Delete task
     */
    public function delete(int $id): bool
    {
        $task = $this->findById($id);
        
        if (!$task) {
            return false;
        }

        return $task->delete();
    }

    /**
     * Get all children of a task recursively
     */
    public function getChildrenRecursive(int $taskId): Collection
    {
        $task = $this->model->with('childrenRecursive')->find($taskId);
        
        return $task ? $task->childrenRecursive : collect();
    }
}
