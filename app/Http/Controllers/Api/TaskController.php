<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * GET api/tasks — list all tasks (paginated) with creator info.
     */
    public function index(): AnonymousResourceCollection
    {
        $tasks = Task::with(['creator', 'assignee'])
            ->latest()
            ->paginate(10);

        return TaskResource::collection($tasks);
    }

    /**
     * GET api/tasks/{task} — show a single task with creator info.
     */
    public function show(Task $task): TaskResource
    {
        $task->load(['creator', 'assignee']);

        return new TaskResource($task);
    }

    /**
     * POST api/tasks — create a new task.
     */
    public function store(Request $request): TaskResource
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'status'      => 'nullable|string|max:50',
            'board_column'=> 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
            'tags'        => 'nullable|array',
            'labels'      => 'nullable|array',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        /** @var User $user */
        $user = $request->user();

        $task = Task::create(array_merge($data, [
            'user_id'    => $user->id,
            'creator_id' => $user->id,
        ]));

        $task->load(['creator', 'assignee']);

        return new TaskResource($task);
    }

    /**
     * PUT api/tasks/{task} — update an existing task.
     */
    public function update(Request $request, Task $task): TaskResource
    {
        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'status'      => 'nullable|string|max:50',
            'board_column'=> 'nullable|string|max:50',
            'completed'   => 'nullable|boolean',
            'color'       => 'nullable|string|max:20',
            'tags'        => 'nullable|array',
            'labels'      => 'nullable|array',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $task->update($data);
        $task->load(['creator', 'assignee']);

        return new TaskResource($task);
    }

    /**
     * DELETE api/tasks/{task} — soft-delete a task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
