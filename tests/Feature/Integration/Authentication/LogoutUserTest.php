<?php

/**
 * Feature: Authentication
 * Purpose: Verify logout revokes the current Sanctum access token for the authenticated shared-auth user.
 * Dependencies: tests/TestCase.php, database/factories/UserFactory.php, /api/auth/logout
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use App\Models\UserPresence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_log_out_and_revoke_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-web');

        UserPresence::query()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
            'last_seen_at' => now(),
        ]);

        $response = $this->withToken($token->plainTextToken)->postJson('/api/auth/logout');

        $response->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseCount('user_presences', 0);
    }
}
