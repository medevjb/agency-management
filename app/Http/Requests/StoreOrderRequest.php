<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Services\PlatformFeeCalculator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Order::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->user()->isSales()) {
            return $this->salesRules();
        }

        return [
            'order_id' => 'nullable|string|unique:orders,order_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:20000',
            'credentials' => 'nullable|string|max:65000',
            'status' => 'sometimes|string',
            'is_repeat' => 'sometimes|boolean',
            'repeat_client_key' => 'nullable|string|max:255',
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
            'sales_shares' => 'nullable|array',
            'sales_shares.*.user_id' => 'required|exists:users,id',
            'sales_shares.*.amount' => 'required|numeric|min:0',
            'client_documents' => 'nullable|array|max:20',
            'client_documents.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,csv,txt,md,json,zip,png,jpg,jpeg,gif,webp',
        ];
    }

    /**
     * Sales people open an order with the deal facts only. Status, progress,
     * team, tech stack, assignees and dates belong to operations.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function salesRules(): array
    {
        return [
            'order_id' => 'nullable|string|unique:orders,order_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:20000',
            'credentials' => 'nullable|string|max:65000',
            'value' => 'nullable|numeric|min:0',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'is_repeat' => 'sometimes|boolean',
            'repeat_client_key' => 'nullable|string|max:255',
            'sales_shares' => 'nullable|array',
            'sales_shares.*.user_id' => 'required|exists:users,id',
            'sales_shares.*.amount' => 'required|numeric|min:0',
            'client_documents' => 'nullable|array|max:20',
            'client_documents.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,csv,txt,md,json,zip,png,jpg,jpeg,gif,webp',
        ];
    }

    /**
     * The split may not promise more than the order is worth.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $shares = $this->input('sales_shares', []);

            if (! is_array($shares) || $shares === []) {
                return;
            }

            $total = round(array_sum(array_map(fn ($share) => (float) ($share['amount'] ?? 0), $shares)), 2);
            $value = round((float) $this->input('value', 0), 2);
            $freelanceProfileId = $this->input('freelance_profile_id');

            // Calculate net revenue after platform fees for comparison
            $netRevenue = $value > 0 ? PlatformFeeCalculator::netFor($value, $freelanceProfileId) : 0;

            if ($total > $netRevenue) {
                $validator->errors()->add(
                    'sales_shares',
                    'The sales credit adds up to $'.number_format($total, 2).', more than the net revenue of $'.number_format($netRevenue, 2).' after platform fees.'
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The client name is required.',
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
