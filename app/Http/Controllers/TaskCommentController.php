<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Task;

class TaskCommentController extends Controller
{
    public function store(StoreCommentRequest $request, Task $task)
    {
        $data = $request->validated();

        $task->comments()->create([
            'user_id' => (int) $data['user_id'],
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('success', 'Comment added successfully.');
    }
}
