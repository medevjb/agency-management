<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'employee_id' => 'nullable|string|regex:/^[0-9]{6,8}$/|unique:users,employee_id',
            'name' => 'required|string|max:255|min:2|regex:/^[a-zA-Z\s\-\.]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'password' => 'required|string|min:8|',
            'password_confirmation' => 'required|string|min:8',
            'department' => 'nullable|string|max:255|regex:/^[a-zA-Z\s\-\.\&]+$/',
            'position' => 'nullable|string|max:255|regex:/^[a-zA-Z\s\-\.\&]+$/',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'salary' => 'nullable|numeric|min:0|max:999999.99',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'role' => 'nullable|string|max:255|exists:roles,name',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    /**
     * Get the custom error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.regex' => 'Employee ID must be a 6-8 digit number.',
            'employee_id.unique' => 'This employee ID is already in use.',
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 2 characters long.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'name.regex' => 'Name can only contain letters, spaces, hyphens, and periods.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.regex' => 'Please enter a valid phone number.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'department.max' => 'Department cannot exceed 255 characters.',
            'department.regex' => 'Department can only contain letters, spaces, hyphens, periods, and ampersands.',
            'position.max' => 'Position cannot exceed 255 characters.',
            'position.regex' => 'Position can only contain letters, spaces, hyphens, periods, and ampersands.',
            'hire_date.date' => 'Please enter a valid hire date.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
            'salary.max' => 'Salary cannot exceed $999,999.99.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'role.exists' => 'Selected role does not exist.',
            'team_id.exists' => 'Selected team does not exist.',
            'tech_stack_id.exists' => 'Selected tech stack does not exist.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }
}
