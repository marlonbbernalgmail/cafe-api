<?php

/**
 * Feature: Authentication
 * Purpose: Carry optional device metadata from the mobile heartbeat request into the presence domain.
 * Dependencies: App\Http\Requests\Authentication\RecordUserPresenceHeartbeatRequest
 */

namespace App\Domain\Authentication\DTOs;

use App\Http\Requests\Authentication\RecordUserPresenceHeartbeatRequest;

readonly class UserPresenceHeartbeatData
{
    public function __construct(
        public ?string $deviceId,
        public ?string $platform,
        public ?string $appVersion,
    ) {}

    public static function fromRequest(RecordUserPresenceHeartbeatRequest $request): self
    {
        $deviceId = trim($request->string('device_id')->toString());
        $platform = trim($request->string('platform')->toString());
        $appVersion = trim($request->string('app_version')->toString());

        return new self(
            deviceId: $deviceId !== '' ? $deviceId : null,
            platform: $platform !== '' ? $platform : null,
            appVersion: $appVersion !== '' ? $appVersion : null,
        );
    }
}
