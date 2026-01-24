<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBugReportRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'bug_priority_id' => 'required|exists:bug_priorities,id',
            'status' => 'required|in:pending,in_progress,resolved,published',
            'admin_notes' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for the bug report.',
            'description.required' => 'Please provide a description of the bug.',
            'bug_priority_id.required' => 'Please select a priority level.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
            'images.max' => 'You can upload a maximum of 5 images.',
            'images.*.image' => 'All files must be images.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, GIF, or WEBP format.',
            'images.*.max' => 'Each image must be no larger than 5MB.',
        ];
    }
}
