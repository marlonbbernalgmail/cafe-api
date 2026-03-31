<?php

/**
 * Feature: Authentication
 * Purpose: Resolve whether a shared-auth user is currently considered online.
 * Dependencies: App\Domain\Authentication\Services\UserPresenceService, App\Models\User
 */

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\UserPresenceData;
use App\Domain\Authentication\Services\UserPresenceService;
use App\Models\User;

class GetUserPresenceAction
{
    public function __construct(
        private readonly UserPresenceService $userPresence,
    ) {}

    public function __invoke(User $user): UserPresenceData
    {
        return $this->userPresence->getPresenceFor($user);
    }
}
