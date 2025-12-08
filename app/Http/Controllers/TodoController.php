<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Repositories\TodoRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function __construct(
        protected TodoRepository $todoRepository
    ) {}

    /**
     * Display a listing of todos
     */
    public function index(Request $request): JsonResponse
    {
        $todos = $this->todoRepository->getAll($request->user()->id);
        return response()->json(TodoResource::collection($todos));
    }

    /**
     * Store a newly created todo
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|default:0'
        ]);

        // Automatycznie przypisz user_id zalogowanego użytkownika
        $validated['user_id'] = $request->user()->id;
        if (!request()->filled('status')) {
            $validated['status'] = 0;
        }
    
        $todo = $this->todoRepository->create($validated);
        return response()->json(new TodoResource($todo), 201);
    }

    /**
     * Display the specified todo
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($id);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        // Załaduj tylko root tasks (bez parent_id) wraz z całym drzewem children rekurencyjnie
        $todo->load(['tasks' => function ($query) {
            $query->whereNull('parent_id')->with('childrenRecursive');
        }]);

        return response()->json(new TodoResource($todo));
    }

    /**
     * Update the specified todo
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($id);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => '',
            'priority' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        if (!request()->filled('status')) {
            $validated['status'] = 0;
        }

        $this->todoRepository->update($id, $validated);
        $todo = $this->todoRepository->findById($id);
        return response()->json(new TodoResource($todo));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $todo = $this->todoRepository->findById($id);
        
        if (!$todo || $todo->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $this->todoRepository->delete($id);
        return response()->json(['message' => 'Todo deleted successfully']);
    }
}
