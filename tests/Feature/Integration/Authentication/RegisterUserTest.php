<?php

/**
 * Feature: Authentication
 * Purpose: Verify user registration returns a Sanctum access token and persists the new shared-auth user.
 * Dependencies: tests/TestCase.php, Illuminate\Foundation\Testing\RefreshDatabase, /api/auth/register
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_access_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'cafe-pos-mobile',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.name', 'Jane Doe')
            ->assertJsonPath('user.email', 'jane@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);
        $this->assertTrue(Hash::check('password', User::query()->firstOrFail()->password));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_whitespace_only_device_name_falls_back_to_the_default_token_name(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => '   ',
        ])->assertCreated();

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'api-client',
        ]);
    }
}
