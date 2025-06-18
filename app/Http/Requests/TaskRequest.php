<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "status" => "required|in:pending,completed",
            "priority" => "required|in:low,medium,high",
            "order" => "required|integer|min:1",
            "dueDate" => "nullable|string",
            "user_id" => "required|integer|exists:users,id"
        ];
    }

    public function messages(): array {
        return [
            'title.required' => 'Task title is required',
            'title.string' => 'Task title must be a string',
            'title.max' => 'Task title cannot exceed 255 characters',
            'description.string' => 'Task description must be a string',
            'status.required' => 'Task status is required',
            'status.in' => 'Task status must be either pending or completed',
            'priority.required' => 'Task priority is required',
            'priority.in' => 'Task priority must be either low, medium, or high',
            'order.required' => 'Task order is required',
            'order.integer' => 'Task order must be a number',
            'order.min' => 'Task order must be at least 1',
            'dueDate.string' => 'Due Date must be a string',
            'user_id.required' => 'User assignment is required',
            'user_id.integer' => 'Invalid user assignment',
            'user_id.exists' => 'The selected user does not exist'
        ];
    }
}
