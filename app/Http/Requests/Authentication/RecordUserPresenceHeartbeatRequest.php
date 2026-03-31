<?php

/**
 * Feature: Authentication
 * Purpose: Validate optional device metadata sent with authenticated presence heartbeat requests.
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class RecordUserPresenceHeartbeatRequest extends FormRequest
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
            'device_id' => ['nullable', 'string', 'min:1', 'max:255'],
            'platform' => ['nullable', 'string', 'min:1', 'max:50'],
            'app_version' => ['nullable', 'string', 'min:1', 'max:50'],
        ];
    }
}
