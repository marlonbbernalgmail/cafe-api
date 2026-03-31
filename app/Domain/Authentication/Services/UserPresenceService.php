<?php

/**
 * Feature: Authentication
 * Purpose: Record authenticated heartbeat activity and resolve whether users are currently online within a configured freshness window.
 * Dependencies: App\Domain\Authentication\DTOs\UserPresenceData, App\Models\User, App\Models\UserPresence
 */

namespace App\Domain\Authentication\Services;

use App\Domain\Authentication\DTOs\UserPresenceData;
use App\Domain\Authentication\DTOs\UserPresenceHeartbeatData;
use App\Models\User;
use App\Models\UserPresence;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class UserPresenceService
{
    public function recordHeartbeat(
        User $user,
        PersonalAccessToken $token,
        UserPresenceHeartbeatData $data,
    ): UserPresenceData {
        $seenAt = now();
        $attributes = [
            'user_id' => $user->getKey(),
            'last_seen_at' => $seenAt,
        ];

        if ($data->deviceId !== null) {
            $attributes['device_id'] = $data->deviceId;
        }

        if ($data->platform !== null) {
            $attributes['platform'] = $data->platform;
        }

        if ($data->appVersion !== null) {
            $attributes['app_version'] = $data->appVersion;
        }

        UserPresence::query()->updateOrCreate(
            [
                'personal_access_token_id' => $token->getKey(),
            ],
            $attributes,
        );

        return $this->getPresenceFor($user);
    }

    public function getPresenceFor(User $user): UserPresenceData
    {
        $onlineWindowSeconds = $this->onlineWindowSeconds();
        $onlineThreshold = now()->subSeconds($onlineWindowSeconds);
        $latestPresence = UserPresence::query()
            ->where('user_id', $user->getKey())
            ->latest('last_seen_at')
            ->first();
        $lastSeenAt = $latestPresence?->last_seen_at;

        return new UserPresenceData(
            userId: $user->getKey(),
            isOnline: $lastSeenAt !== null && $lastSeenAt->greaterThanOrEqualTo($onlineThreshold),
            lastSeenAt: $lastSeenAt,
            onlineUsersCount: $this->countOnlineUsers($onlineThreshold),
            onlineWindowSeconds: $onlineWindowSeconds,
        );
    }

    private function countOnlineUsers(Carbon $onlineThreshold): int
    {
        return (int) UserPresence::query()
            ->online($onlineThreshold)
            ->distinct()
            ->count('user_id');
    }

    private function onlineWindowSeconds(): int
    {
        return max(1, (int) config('authentication.presence_timeout_seconds', 300));
    }
}
