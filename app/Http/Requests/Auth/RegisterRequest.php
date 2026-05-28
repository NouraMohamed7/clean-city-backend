<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'in:user,company,admin'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'company.name' => ['required_if:role,company', 'string', 'max:255'],
            'company.city_id' => ['required_if:role,company', 'exists:cities,id'],
            'company.phone' => ['nullable', 'string'],
            'company.latitude' => ['required_if:role,company', 'numeric'],
'company.longitude' => ['required_if:role,company', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'company.name.required_if' => 'Company name is required for company accounts.',
        ];
    }
}
