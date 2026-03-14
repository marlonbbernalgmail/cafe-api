<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiPingTest extends TestCase
{
    public function test_the_api_ping_endpoint_returns_a_successful_response(): void
    {
        $response = $this->getJson('/api/ping');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'pong',
            ]);
    }
}
