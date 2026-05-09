<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::withTrashed()
            ->with(['creator', 'assignee', 'comments.user'])
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $counts = [
            'total' => Task::count(),
            'completed' => Task::where('completed', true)->count(),
            'urgent' => Task::where('priority', 'urgent')->count(),
            'pending' => Task::where('completed', false)->count(),
        ];

        return view('tasks.index', compact('tasks', 'counts'));
    }

    public function create()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('tasks.create', compact('users'));
    }

    public function show(int $id)
    {
        $task = Task::withTrashed()
            ->with(['creator', 'assignee', 'comments.user'])
            ->findOrFail($id);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('tasks.show', compact('task', 'users'));
    }

    public function edit(int $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        $data = $request->validated();

        $task = Task::withTrashed()->findOrFail($id);

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'completed' => (bool) $request->boolean('completed'),
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'board_column' => $data['board_column'] ?? 'To Do',
            'user_id' => (int) $data['creator_id'],
            'creator_id' => (int) $data['creator_id'],
            'assignee_id' => (int) $data['assignee_id'],
            'assigned_to' => User::query()->whereKey((int) $data['assignee_id'])->value('name'),
            'color' => $data['color'] ?? '#3b82f6',
            'tags' => $this->normalizeCsvList($data['tags'] ?? null),
            'labels' => $this->normalizeCsvList($data['labels'] ?? null),
        ]);

        return redirect()->route('tasks.show', $id)->with('success', 'Task updated successfully');
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();

        Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'user_id' => (int) $data['creator_id'],
            'creator_id' => (int) $data['creator_id'],
            'assignee_id' => (int) $data['assignee_id'],
            'assigned_to' => User::query()->whereKey((int) $data['assignee_id'])->value('name'),
            'board_column' => 'To Do',
            'color' => '#3b82f6',
            'completed' => false,
            'tags' => [],
            'labels' => [],
            'subtasks' => [],
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    public function destroy(int $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted');
    }

    public function restore(int $id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->route('tasks.index')->with('success', 'Task restored successfully');
    }

    private function normalizeCsvList(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $parts = explode(',', $value);

        return collect($parts)
            ->map(fn (string $part) => trim($part))
            ->filter()
            ->values()
            ->all();
    }
}
