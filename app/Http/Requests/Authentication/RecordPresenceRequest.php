<?php

/**
 * Feature: Presence Heartbeat
 * Purpose: Validate presence ping requests before the presence domain action runs.
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class RecordPresenceRequest extends FormRequest
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
            'app_version' => ['required', 'string', 'max:32'],
            'device_id' => ['required', 'string', 'max:128'],
            'platform' => ['required', 'string', 'max:32'],
        ];
    }
}
