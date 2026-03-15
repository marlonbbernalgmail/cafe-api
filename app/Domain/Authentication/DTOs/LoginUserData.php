<?php

/**
 * Feature: Authentication
 * Purpose: Carry validated login input into the authentication domain.
 * Dependencies: App\Http\Requests\Authentication\LoginUserRequest
 */

namespace App\Domain\Authentication\DTOs;

use App\Http\Requests\Authentication\LoginUserRequest;

readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $deviceName,
    ) {
    }

    public static function fromRequest(LoginUserRequest $request): self
    {
        $deviceName = trim($request->string('device_name')->toString());

        return new self(
            email: $request->string('email')->lower()->toString(),
            password: $request->string('password')->toString(),
            deviceName: $deviceName !== '' ? $deviceName : null,
        );
    }
}
