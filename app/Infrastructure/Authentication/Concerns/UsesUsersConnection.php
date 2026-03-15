<?php

/**
 * Feature: Authentication
 * Purpose: Resolve auth persistence models to the shared users database when the host API configures one.
 * Dependencies: config/authentication.php, Illuminate\Database\Eloquent\Model
 */

namespace App\Infrastructure\Authentication\Concerns;

trait UsesUsersConnection
{
    public function getConnectionName(): ?string
    {
        return config('authentication.users_connection') ?: parent::getConnectionName();
    }
}
