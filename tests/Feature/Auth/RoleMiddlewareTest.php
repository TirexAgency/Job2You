<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_candidate_cannot_access_admin_routes(): void
    {
        $candidate = User::factory()->candidate()->create();

        $response = $this->actingAs($candidate)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_candidate_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/candidate/dashboard');

        $response->assertStatus(200);
    }

    public function test_candidate_can_access_candidate_routes(): void
    {
        $candidate = User::factory()->candidate()->create();

        $response = $this->actingAs($candidate)->get('/candidate/dashboard');

        $response->assertStatus(200);
    }
}
