<?php

namespace App\Http\Requests;

use App\Services\BugTriageService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBugReportRequest extends FormRequest
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
     * Category/priority are only optional when AI triage is actually
     * configured (ANTHROPIC_API_KEY set) — otherwise nothing would ever
     * fill them in, so fall back to requiring them as before.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(BugTriageService $bugTriageService): array
    {
        $categoryPriorityRule = $bugTriageService->isConfigured() ? 'nullable' : 'required';

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'bug_category_id' => "{$categoryPriorityRule}|exists:bug_categories,id",
            'bug_priority_id' => "{$categoryPriorityRule}|exists:bug_priorities,id",
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
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
            'bug_category_id.required' => 'Please select a category.',
            'bug_category_id.exists' => 'Please select a valid category.',
            'bug_priority_id.required' => 'Please select a priority level.',
            'bug_priority_id.exists' => 'Please select a valid priority level.',
            'images.max' => 'You can upload a maximum of 5 images.',
            'images.*.image' => 'All files must be images.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, GIF, or WEBP format.',
            'images.*.max' => 'Each image must be no larger than 5MB.',
        ];
    }
}
