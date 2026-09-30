<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    // ============================================================
    // Étape 9 : Route POST /logout + méthode logout()
    // ============================================================

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_logout_redirects_to_login_with_success_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Vous avez été déconnecté avec succès.');
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
    }

    // ============================================================
    // Étape 10 : Destruction session complète
    // ============================================================

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['private_value' => 'must be removed'])
            ->post('/logout')
            ->assertSessionMissing('private_value');

        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_logout_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/dashboard');
        $tokenBefore = session()->token();
        $response = $this->post('/logout');

        $this->assertNotSame($tokenBefore, session()->token());
    }

    public function test_logout_rotates_remember_token(): void
    {
        $user = User::factory()->create();
        $rememberTokenBefore = $user->getRememberToken();

        $this->actingAs($user)->post('/logout');

        $this->assertNotSame($rememberTokenBefore, $user->refresh()->getRememberToken());
    }

    // ============================================================
    // Étape 11 : Redirection avec message
    // ============================================================

    public function test_logout_does_not_leak_info_in_url(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        // La redirection ne contient aucune information sensible dans l'URL
        $this->assertStringNotContainsString('token', $response->headers->get('Location'));
        $this->assertStringNotContainsString('session', $response->headers->get('Location'));
        $this->assertStringNotContainsString('user', $response->headers->get('Location'));
    }

    public function test_logout_message_displayed_on_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        // Suivre la redirection et vérifier le message
        $response = $this->get('/login');
        $response->assertSee('Vous avez été déconnecté avec succès.');
    }

    // ============================================================
    // Cas multi-onglets
    // ============================================================

    public function test_logout_invalidates_all_sessions(): void
    {
        $user = User::factory()->create();

        // Simuler deux sessions (deux "onglets")
        $this->actingAs($user)->get('/dashboard');

        // Déconnexion
        $this->actingAs($user)->post('/logout');

        // L'utilisateur ne peut plus accéder aux routes protégées
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/profile')->assertRedirect(route('login'));
    }

    public function test_logout_prevents_access_to_admin_routes(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->post('/logout');

        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }
}
