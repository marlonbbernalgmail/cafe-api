<?php

/**
 * Feature: Authentication
 * Purpose: Issue Sanctum API tokens for authenticated users through a reusable domain service.
 * Dependencies: App\Domain\Authentication\DTOs\IssuedApiTokenData, App\Models\User
 */

namespace App\Domain\Authentication\Services;

use App\Domain\Authentication\DTOs\IssuedApiTokenData;
use App\Models\User;

class IssueUserApiTokenService
{
    public function issue(User $user, ?string $deviceName = null): IssuedApiTokenData
    {
        $tokenName = $deviceName ?: 'api-client';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return new IssuedApiTokenData(
            user: $user,
            accessToken: $plainTextToken,
        );
    }
}
