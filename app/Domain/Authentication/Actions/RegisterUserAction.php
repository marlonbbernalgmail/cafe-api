<?php

/**
 * Feature: Authentication
 * Purpose: Register a new shared-auth user and issue the first API token.
 * Dependencies: App\Domain\Authentication\DTOs\RegisterUserData, App\Domain\Authentication\Services\IssueUserApiTokenService, App\Models\User
 */

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\IssuedApiTokenData;
use App\Domain\Authentication\DTOs\RegisterUserData;
use App\Domain\Authentication\Services\IssueUserApiTokenService;
use App\Models\User;

class RegisterUserAction
{
    public function __construct(
        private readonly IssueUserApiTokenService $issueUserApiToken,
    ) {
    }

    public function __invoke(RegisterUserData $data): IssuedApiTokenData
    {
        $user = User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        return $this->issueUserApiToken->issue($user, $data->deviceName);
    }
}
