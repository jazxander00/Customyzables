<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(array_keys(Role::options()))],
            'barangay' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Choose whether you are donating or upcycling.',
            'email.unique' => 'That email already has an account. Try signing in instead.',
        ];
    }
}
