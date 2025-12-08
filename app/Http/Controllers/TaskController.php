<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use App\Repositories\TodoRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepository $taskRepository,
        protected TodoRepository $todoRepository
    ) {}

    /**
     * Display a listing of tasks for a specific todo
     */
    public function index(Request $request, int $todoId): JsonResponse
    {
        $todo = $this->todoRepository->findById($todoId);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $tasks = $this->taskRepository->getAllForTodo($todoId);
        
        return response()->json(TaskResource::collection($tasks));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request, int $todoId): JsonResponse
    {
        $todo = $this->todoRepository->findById($todoId);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:tasks,id',
            'status' => 'nullable|integer',
        ]);

        $validated['todo_id'] = $todoId;
        $validated['status'] = 0;

        $task = $this->taskRepository->create($validated);
        
        return response()->json(new TaskResource($task), 201);
    }

    /**
     * Display the specified task
     */
    public function show(Request $request, int $todoId, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($todoId);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $task = $this->taskRepository->findByIdAndTodo($id, $todoId, true);
        
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json(new TaskResource($task));
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, int $todoId, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($todoId);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $task = $this->taskRepository->findByIdAndTodo($id, $todoId);
        
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'task' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:tasks,id',
            'status' => 'nullable|integer',
        ]);

        $this->taskRepository->update($id, $validated);
        $task = $this->taskRepository->findById($id);
        
        return response()->json(new TaskResource($task));
    }

    /**
     * Remove the specified task
     */
    public function destroy(Request $request, int $todoId, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($todoId);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $task = $this->taskRepository->findByIdAndTodo($id, $todoId);
        
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $this->taskRepository->delete($id);
        
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
