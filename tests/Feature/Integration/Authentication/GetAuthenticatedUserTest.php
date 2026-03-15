<?php

/**
 * Feature: Authentication
 * Purpose: Verify authenticated token requests can resolve the current user through the auth API.
 * Dependencies: tests/TestCase.php, database/factories/UserFactory.php, /api/auth/me
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_be_returned_from_me_endpoint(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $token = $user->createToken('cafe-pos-mobile')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', 'jane@example.com');
    }
}
