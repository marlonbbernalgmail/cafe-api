<?php

/**
 * Feature: Authentication
 * Purpose: Track the latest heartbeat received for each authenticated mobile token so user presence can be derived safely.
 * Dependencies: App\Infrastructure\Authentication\Concerns\UsesUsersConnection, App\Models\User
 */

namespace App\Models;

use App\Infrastructure\Authentication\Concerns\UsesUsersConnection;
use App\Infrastructure\Authentication\Models\PersonalAccessToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class UserPresence extends Model
{
    use UsesUsersConnection;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'personal_access_token_id',
        'last_seen_at',
        'device_id',
        'platform',
        'app_version',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function personalAccessToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class);
    }

    public function scopeOnline(Builder $query, Carbon $threshold): Builder
    {
        return $query->where('last_seen_at', '>=', $threshold);
    }
}
