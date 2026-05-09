<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'unique:tasks,title'],
            'description' => ['required', 'string', 'min:10'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:to-do,in_progress,done'],
            'creator_id' => ['required', 'exists:users,id'],
            'assignee_id' => ['required', 'exists:users,id'],
            'completed' => ['nullable', 'boolean'],
            'board_column' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'tags' => ['nullable', 'string'],
            'labels' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a task title.',
            'title.min' => 'The task title must be at least 3 characters.',
            'title.unique' => 'This title already exists. Please choose a different title.',
            'description.required' => 'Please provide a task description.',
            'description.min' => 'The task description must be at least 10 characters.',
            'due_date.required' => 'Please select a due date.',
            'due_date.date' => 'The due date must be a valid date.',
            'priority.required' => 'Please select a priority.',
            'priority.in' => 'Priority must be low, medium, high, or urgent.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be to-do, in_progress, or done.',
            'creator_id.required' => 'Please select a creator.',
            'creator_id.exists' => 'The selected creator is invalid.',
            'assignee_id.required' => 'Please select an assignee.',
            'assignee_id.exists' => 'The selected assignee is invalid.',
        ];
    }
}
