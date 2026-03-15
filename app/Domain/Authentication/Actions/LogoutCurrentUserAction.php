<?php

/**
 * Feature: Authentication
 * Purpose: Revoke the currently authenticated Sanctum token for the active shared-auth user.
 * Dependencies: Illuminate\Contracts\Auth\Authenticatable
 */

namespace App\Domain\Authentication\Actions;

use Illuminate\Contracts\Auth\Authenticatable;

class LogoutCurrentUserAction
{
    public function __invoke(Authenticatable $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
