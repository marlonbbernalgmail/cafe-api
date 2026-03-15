<?php

/**
 * Feature: Authentication
 * Purpose: Validate registration requests before they enter the authentication domain.
 * Dependencies: App\Models\User, Illuminate\Foundation\Http\FormRequest, Illuminate\Validation\Rule
 */

namespace App\Http\Requests\Authentication;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'device_name' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }
}
