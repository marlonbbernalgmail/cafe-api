<?php

/**
 * Feature: Presence Heartbeat
 * Purpose: Stamp the current Sanctum access token with the latest presence metadata.
 * Dependencies: App\Domain\Authentication\DTOs\RecordPresenceData, Illuminate\Contracts\Auth\Authenticatable
 */

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\RecordPresenceData;
use Illuminate\Contracts\Auth\Authenticatable;

class RecordPresenceAction
{
    public function __invoke(Authenticatable $user, RecordPresenceData $data): void
    {
        $user->currentAccessToken()?->forceFill([
            'presence_pinged_at' => now(),
            'presence_app_version' => $data->appVersion,
            'presence_device_id' => $data->deviceId,
            'presence_platform' => $data->platform,
        ])->save();
    }
}
