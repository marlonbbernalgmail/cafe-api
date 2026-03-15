<?php

/**
 * Feature: Authentication
 * Purpose: Persist Sanctum personal access tokens on the shared users database for portable auth reuse.
 * Dependencies: App\Infrastructure\Authentication\Concerns\UsesUsersConnection, Laravel\Sanctum\PersonalAccessToken
 */

namespace App\Infrastructure\Authentication\Models;

use App\Infrastructure\Authentication\Concerns\UsesUsersConnection;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UsesUsersConnection;
}
