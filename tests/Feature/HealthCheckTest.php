<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_returns_ok(): void
    {
        $response = $this->get(route('health'));

        $response->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }
}
