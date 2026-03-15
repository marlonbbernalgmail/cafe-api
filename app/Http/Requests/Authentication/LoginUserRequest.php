<?php

/**
 * Feature: Authentication
 * Purpose: Validate login requests before credential verification runs in the authentication domain.
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }
}
