<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'project_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'project_date' => 'nullable|date',
            'status' => 'in:draft,published,archived',
            'is_featured' => 'boolean',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'tech_stack_id.exists' => 'Selected tech stack is invalid.',
            'project_url.url' => 'Project URL must be a valid URL.',
            'github_url.url' => 'GitHub URL must be a valid URL.',
            'screenshot.image' => 'Screenshot must be an image file.',
            'screenshot.max' => 'Screenshot size must not exceed 2MB.',
        ];
    }
}
