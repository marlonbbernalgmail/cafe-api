<?php

/**
 * Feature: Authentication
 * Purpose: Verify login returns a Sanctum access token only for valid shared-auth user credentials.
 * Dependencies: tests/TestCase.php, database/factories/UserFactory.php, /api/auth/login
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_receive_access_token(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'password',
            'device_name' => 'cafe-pos-web',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'jane@example.com');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
            'device_name' => 'cafe-pos-web',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_whitespace_only_device_name_falls_back_to_the_default_token_name(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'password',
            'device_name' => '   ',
        ])->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'api-client',
        ]);
    }
}
