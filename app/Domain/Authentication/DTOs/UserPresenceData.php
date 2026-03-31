<?php

/**
 * Feature: Authentication
 * Purpose: Represent a user's current online presence plus the active-user count for client consumption.
 * Dependencies: Carbon\CarbonInterface
 */

namespace App\Domain\Authentication\DTOs;

use Carbon\CarbonInterface;

readonly class UserPresenceData
{
    public function __construct(
        public int $userId,
        public bool $isOnline,
        public ?CarbonInterface $lastSeenAt,
        public int $onlineUsersCount,
        public int $onlineWindowSeconds,
    ) {}

    /**
     * @return array<string, bool|int|string|null>
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'last_seen_at' => $this->lastSeenAt?->toISOString(),
            'online_users_count' => $this->onlineUsersCount,
            'online_window_seconds' => $this->onlineWindowSeconds,
        ];
    }
}
