<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in-progress,completed,on-hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|integer|min:0',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'status.in' => 'The status must be one of: pending, in-progress, completed, or on-hold.',
            'priority.in' => 'The priority must be one of: low, medium, high, or urgent.',
            'assigned_to.exists' => 'The selected user does not exist.',
            'project_id.exists' => 'The selected project does not exist.',
        ];
    }
}
