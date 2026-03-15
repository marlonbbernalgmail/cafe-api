<?php

/**
 * Feature: Authentication
 * Purpose: Verify logout revokes the current Sanctum access token for the authenticated shared-auth user.
 * Dependencies: tests/TestCase.php, database/factories/UserFactory.php, /api/auth/logout
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_log_out_and_revoke_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-web')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
