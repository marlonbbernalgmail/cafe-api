<?php

/**
 * Feature: Authentication
 * Purpose: Verify mobile heartbeat presence tracking can refresh active tokens, count distinct online users, and expire stale activity.
 * Dependencies: tests/TestCase.php, App\Models\UserPresence, /api/auth/presence, /api/users/{user}/presence
 */

namespace Tests\Feature\Integration\Authentication;

use App\Models\User;
use App\Models\UserPresence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserPresenceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_authenticated_mobile_heartbeat_records_and_refreshes_presence_for_the_current_token(): void
    {
        config()->set('authentication.presence_timeout_seconds', 300);

        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-mobile');

        Carbon::setTestNow('2026-03-31 12:00:00');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/auth/presence', [
                'device_id' => 'android-tablet-01',
                'platform' => 'android',
                'app_version' => '1.4.2',
            ])
            ->assertOk()
            ->assertJsonPath('presence.user_id', $user->id)
            ->assertJsonPath('presence.is_online', true)
            ->assertJsonPath('presence.online_users_count', 1)
            ->assertJsonPath('presence.online_window_seconds', 300);

        Carbon::setTestNow('2026-03-31 12:02:00');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/auth/presence')
            ->assertOk()
            ->assertJsonPath('presence.user_id', $user->id)
            ->assertJsonPath('presence.is_online', true)
            ->assertJsonPath('presence.online_users_count', 1);

        $this->assertDatabaseCount('user_presences', 1);
        $this->assertDatabaseHas('user_presences', [
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
            'device_id' => 'android-tablet-01',
            'platform' => 'android',
            'app_version' => '1.4.2',
        ]);
        $this->assertTrue(
            UserPresence::query()->firstOrFail()->last_seen_at->equalTo(now()),
        );
    }

    public function test_authenticated_mobile_heartbeat_can_store_device_metadata_without_requiring_it_on_every_ping(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-mobile');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/auth/presence', [
                'device_id' => 'iphone-15-pro',
                'platform' => 'ios',
                'app_version' => '2.0.0',
            ])
            ->assertOk();

        $this->withToken($token->plainTextToken)
            ->postJson('/api/auth/presence')
            ->assertOk();

        $this->assertDatabaseHas('user_presences', [
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
            'device_id' => 'iphone-15-pro',
            'platform' => 'ios',
            'app_version' => '2.0.0',
        ]);
    }

    public function test_presence_endpoint_counts_distinct_online_users_even_when_one_user_has_multiple_active_tokens(): void
    {
        config()->set('authentication.presence_timeout_seconds', 300);
        Carbon::setTestNow('2026-03-31 12:00:00');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $primaryToken = $user->createToken('device-one');
        $secondaryToken = $user->createToken('device-two');
        $otherUserToken = $otherUser->createToken('device-three');

        UserPresence::query()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $primaryToken->accessToken->id,
            'last_seen_at' => now(),
        ]);
        UserPresence::query()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $secondaryToken->accessToken->id,
            'last_seen_at' => now(),
        ]);
        UserPresence::query()->create([
            'user_id' => $otherUser->id,
            'personal_access_token_id' => $otherUserToken->accessToken->id,
            'last_seen_at' => now(),
        ]);

        $this->withToken($primaryToken->plainTextToken)
            ->getJson("/api/users/{$user->id}/presence")
            ->assertOk()
            ->assertJsonPath('presence.user_id', $user->id)
            ->assertJsonPath('presence.is_online', true)
            ->assertJsonPath('presence.online_users_count', 2)
            ->assertJsonPath('presence.online_window_seconds', 300);
    }

    public function test_presence_endpoint_marks_a_user_offline_once_the_heartbeat_is_older_than_the_timeout_window(): void
    {
        config()->set('authentication.presence_timeout_seconds', 300);
        Carbon::setTestNow('2026-03-31 12:10:00');

        $user = User::factory()->create();
        $token = $user->createToken('cafe-pos-mobile');

        UserPresence::query()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
            'last_seen_at' => now()->subMinutes(6),
        ]);

        $this->withToken($token->plainTextToken)
            ->getJson("/api/users/{$user->id}/presence")
            ->assertOk()
            ->assertJsonPath('presence.user_id', $user->id)
            ->assertJsonPath('presence.is_online', false)
            ->assertJsonPath('presence.online_users_count', 0)
            ->assertJsonPath('presence.online_window_seconds', 300);
    }
}
