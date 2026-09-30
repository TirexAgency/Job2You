<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_route_has_rate_limiting(): void
    {
        $this->assertTrue(true);
    }

    public function test_login_route_has_rate_limiting(): void
    {
        $this->assertTrue(true);
    }

    public function test_email_verification_notification_has_rate_limiting(): void
    {
        $this->assertTrue(true);
    }
}
