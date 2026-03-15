<?php

/**
 * Feature: Authentication
 * Purpose: Verify the end-to-end register, inspect current user, and logout token flow for the auth API.
 * Dependencies: tests/TestCase.php, Illuminate\Foundation\Testing\RefreshDatabase, auth API endpoints
 */

namespace Tests\Feature\E2E\Authentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateUserFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_check_current_user_and_log_out(): void
    {
        $registerResponse = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'cafe-pos-mobile',
        ]);

        $token = $registerResponse->json('access_token');

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'jane@example.com');

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertNoContent();

        // Force Sanctum to resolve the guard again so the revoked token is rejected.
        app('auth')->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }
}
