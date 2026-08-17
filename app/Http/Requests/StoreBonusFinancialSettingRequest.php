<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBonusFinancialSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'bonus_percentage' => ['required', 'decimal:0,6', 'gte:0', 'lte:0.25'],
            'usd_to_bdt_rate' => ['required', 'decimal:0,4', 'gt:0', 'lte:10000'],
            'effective_from' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return ['bonus_percentage.lte' => 'The bonus percentage cannot exceed 25%.', 'usd_to_bdt_rate.gt' => 'The USD to BDT rate must be greater than zero.'];
    }
}
