<?php

/**
 * Feature: Authentication
 * Purpose: Carry validated registration input into the authentication domain.
 * Dependencies: App\Http\Requests\Authentication\RegisterUserRequest
 */

namespace App\Domain\Authentication\DTOs;

use App\Http\Requests\Authentication\RegisterUserRequest;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $deviceName,
    ) {
    }

    public static function fromRequest(RegisterUserRequest $request): self
    {
        $deviceName = trim($request->string('device_name')->toString());

        return new self(
            name: $request->string('name')->toString(),
            email: $request->string('email')->lower()->toString(),
            password: $request->string('password')->toString(),
            deviceName: $deviceName !== '' ? $deviceName : null,
        );
    }
}
