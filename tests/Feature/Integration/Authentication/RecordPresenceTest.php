<?php

/**
 * Feature: Presence Heartbeat
 * Purpose: Verify that an authenticated device can ping the presence endpoint and have its token stamped with presence metadata.
 * Dependencies: tests/TestCase.php, database/factories/UserFactory.php, /api/auth/presence
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordPresenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_ping_presence_and_token_is_stamped(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-mobile')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/presence', [
            'app_version' => '1.2.3',
            'device_id' => 'device-abc-123',
            'platform' => 'android',
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('personal_access_tokens', [
            'presence_app_version' => '1.2.3',
            'presence_device_id' => 'device-abc-123',
            'presence_platform' => 'android',
        ]);
        $this->assertNotNull(
            \Laravel\Sanctum\PersonalAccessToken::first()?->presence_pinged_at,
        );
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson('/api/auth/presence', [
            'app_version' => '1.2.3',
            'device_id' => 'device-abc-123',
            'platform' => 'android',
        ]);

        $response->assertUnauthorized();
    }

    public function test_presence_request_fails_validation_when_required_fields_are_missing(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-mobile')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/presence', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['app_version', 'device_id', 'platform']);
    }
}
