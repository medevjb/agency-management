<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:profiles,username|regex:/^[a-zA-Z0-9_]+$/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_name.required' => 'Profile name is required.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.max' => 'Avatar size must not exceed 2MB.',
        ];
    }
}
