<?php

namespace App\Repositories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

class TodoRepository
{
    public function __construct(
        protected Todo $model
    ) {}

    /**
     * Get all todos for authenticated user
     */
    public function getAll(?int $userId = null): Collection
    {   
        if ($userId) {
            return $this->model->where('user_id', $userId)->get();
        }
        return $this->model->all();
    }

    /**
     * Find todo by ID
     */
    public function findById(int $id): ?Todo
    {
        return $this->model->find($id);
    }

    /**
     * Create new todo
     */
    public function create(array $data): Todo
    {
        return $this->model->create($data);
    }

    /**
     * Update existing todo
     */
    public function update(int $id, array $data): bool
    {
        $todo = $this->findById($id);
        
        if (!$todo) {
            return false;
        }

        return $todo->update($data);
    }

    /**
     * Delete todo
     */
    public function delete(int $id): bool
    {
        $todo = $this->findById($id);
        
        if (!$todo) {
            return false;
        }

        return $todo->delete();
    }
}
