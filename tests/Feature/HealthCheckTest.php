<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_the_health_check_endpoint_returns_a_successful_response(): void
    {
        $this->get('/up')->assertOk();
    }
}
