<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_preserves_intended_destination_and_regenerates_session(): void
    {
        $user = User::factory()->create();

        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $sessionIdBefore = session()->getId();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertNotSame($sessionIdBefore, session()->getId());
        $this->assertAuthenticatedAs($user);
    }

    public function test_remember_me_issues_the_guard_recaller_cookie(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertCookie(auth()->guard()->getRecallerName());
        $this->assertNotNull($user->fresh()->getRememberToken());
    }

    public function test_admin_login_uses_dashboard_with_role_specific_content(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertSee('Espace administrateur');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors([
            'email' => 'Identifiants incorrects ou compte non disponible.',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_unverified_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'Identifiants incorrects ou compte non disponible.',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_pending_status(): void
    {
        $user = User::factory()->pending()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'Identifiants incorrects ou compte non disponible.',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_suspended_status(): void
    {
        $user = User::factory()->suspended()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'Identifiants incorrects ou compte non disponible.',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
