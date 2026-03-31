<?php

/**
 * Feature: Presence Heartbeat
 * Purpose: Carry validated presence input into the authentication domain.
 * Dependencies: App\Http\Requests\Authentication\RecordPresenceRequest
 */

namespace App\Domain\Authentication\DTOs;

use App\Http\Requests\Authentication\RecordPresenceRequest;

readonly class RecordPresenceData
{
    public function __construct(
        public string $appVersion,
        public string $deviceId,
        public string $platform,
    ) {
    }

    public static function fromRequest(RecordPresenceRequest $request): self
    {
        return new self(
            appVersion: $request->string('app_version')->toString(),
            deviceId: $request->string('device_id')->toString(),
            platform: $request->string('platform')->toString(),
        );
    }
}
