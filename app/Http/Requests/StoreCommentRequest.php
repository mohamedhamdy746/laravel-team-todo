<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'min:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a commenter.',
            'user_id.exists' => 'The selected commenter is invalid.',
            'body.required' => 'Please write a comment before submitting.',
            'body.min' => 'Comment text should be at least 2 characters.',
        ];
    }
}
