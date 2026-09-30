<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_contains_csrf_token(): void
    {
        $response = $this->get('/register');

        $response->assertSee('csrf-token');
    }

    public function test_login_form_contains_csrf_token(): void
    {
        $response = $this->get('/login');

        $response->assertSee('csrf-token');
    }

    public function test_forgot_password_form_contains_csrf_token(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertSee('csrf-token');
    }
}
