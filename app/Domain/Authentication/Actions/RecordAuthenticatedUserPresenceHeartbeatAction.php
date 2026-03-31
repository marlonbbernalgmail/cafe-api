<?php

/**
 * Feature: Authentication
 * Purpose: Persist a fresh heartbeat for the authenticated token and return the user's current presence state.
 * Dependencies: App\Domain\Authentication\Services\UserPresenceService, App\Models\User
 */

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\UserPresenceData;
use App\Domain\Authentication\DTOs\UserPresenceHeartbeatData;
use App\Domain\Authentication\Services\UserPresenceService;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class RecordAuthenticatedUserPresenceHeartbeatAction
{
    public function __construct(
        private readonly UserPresenceService $userPresence,
    ) {}

    public function __invoke(
        User $user,
        PersonalAccessToken $token,
        UserPresenceHeartbeatData $data,
    ): UserPresenceData {
        return $this->userPresence->recordHeartbeat($user, $token, $data);
    }
}
