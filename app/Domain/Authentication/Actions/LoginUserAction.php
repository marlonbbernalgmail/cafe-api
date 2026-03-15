<?php

/**
 * Feature: Authentication
 * Purpose: Verify shared-auth user credentials and issue a new API token.
 * Dependencies: App\Domain\Authentication\DTOs\LoginUserData, App\Domain\Authentication\Services\IssueUserApiTokenService, App\Models\User
 */

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\IssuedApiTokenData;
use App\Domain\Authentication\DTOs\LoginUserData;
use App\Domain\Authentication\Services\IssueUserApiTokenService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUserAction
{
    public function __construct(
        private readonly IssueUserApiTokenService $issueUserApiToken,
    ) {
    }

    public function __invoke(LoginUserData $data): IssuedApiTokenData
    {
        $user = User::query()->where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->issueUserApiToken->issue($user, $data->deviceName);
    }
}
