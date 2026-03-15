<?php

/**
 * Feature: Authentication
 * Purpose: Represent the authenticated user payload returned by the reusable auth API flows.
 * Dependencies: App\Models\User
 */

namespace App\Domain\Authentication\DTOs;

use App\Models\User;

readonly class IssuedApiTokenData
{
    public function __construct(
        public User $user,
        public string $accessToken,
        public string $tokenType = 'Bearer',
    ) {
    }
}
