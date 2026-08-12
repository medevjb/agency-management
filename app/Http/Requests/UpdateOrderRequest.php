<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('order')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $orderId = $this->route('order')->id;

        if ($this->user()->isSales()) {
            return $this->salesRules($orderId);
        }

        return [
            'order_id' => 'nullable|string|unique:orders,order_id,'.$orderId,
            'name' => 'required|string|max:255|unique:orders,name,'.$orderId,
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:20000',
            'credentials' => 'nullable|string|max:65000',
            'status' => 'required|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'priority' => 'required|in:low,medium,high',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_revenue' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'special_status' => 'nullable|in:selected_for_delivery,continue_for_next,stuck_order',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'client_documents' => 'nullable|array|max:20',
            'client_documents.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,csv,txt,md,json,zip,png,jpg,jpeg,gif,webp',
        ];
    }

    /**
     * Sales people may only change the deal facts they own.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function salesRules(int $orderId): array
    {
        return [
            'order_id' => 'nullable|string|unique:orders,order_id,'.$orderId,
            'name' => 'required|string|max:255|unique:orders,name,'.$orderId,
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:20000',
            'credentials' => 'nullable|string|max:65000',
            'value' => 'nullable|numeric|min:0',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'client_documents' => 'nullable|array|max:20',
            'client_documents.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,csv,txt,md,json,zip,png,jpg,jpeg,gif,webp',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The client name is required.',
            'name.unique' => 'Another order already uses this client name.',
            'order_id.unique' => 'This order ID is already in use.',
            'status.required' => 'Please select a status for the order.',
            'priority.in' => 'The priority must be low, medium, or high.',
            'progress.integer' => 'Progress must be a whole number.',
            'progress.min' => 'Progress cannot be lower than 0%.',
            'progress.max' => 'Progress cannot be higher than 100%.',
            'end_date.after_or_equal' => 'The deadline must be on or after the start date.',
            'client_documents.max' => 'You can upload at most 20 documents at a time.',
            'client_documents.*.max' => 'Each document must be 20 MB or smaller.',
            'client_documents.*.mimes' => 'Documents must be a PDF, Office file, text file, archive, or image.',
        ];
    }
}
