<?php

/**
 * Feature: Authentication
 * Purpose: Verify auth models can be pointed at a shared users database connection for portable reuse across APIs.
 * Dependencies: app/Models/User.php, app/Infrastructure/Authentication/Models/PersonalAccessToken.php
 */

namespace Tests\Unit\Authentication;

use App\Infrastructure\Authentication\Models\PersonalAccessToken;
use App\Models\User;
use Tests\TestCase;

class UsesUsersConnectionTest extends TestCase
{
    public function test_auth_models_use_the_configured_shared_users_connection(): void
    {
        config()->set('authentication.users_connection', 'users');

        $this->assertSame('users', (new User())->getConnectionName());
        $this->assertSame('users', (new PersonalAccessToken())->getConnectionName());
    }

    public function test_auth_models_fall_back_to_the_default_connection_when_not_configured(): void
    {
        config()->set('authentication.users_connection', null);

        $this->assertNull((new User())->getConnectionName());
        $this->assertNull((new PersonalAccessToken())->getConnectionName());
    }
}
