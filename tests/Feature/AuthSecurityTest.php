<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Rate limiting
    // ---------------------------------------------------------------

    public function test_register_is_rate_limited_to_5_attempts_per_minute(): void
    {
        // On utilise des données volontairement invalides : l'inscription
        // échoue, personne n'est connecté, et le middleware 'guest' ne
        // redirige pas. Le middleware 'throttle' compte donc chaque
        // tentative (c'est exactement le cas d'attaque brute-force).
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', $this->invalidRegistrationData())
                ->assertStatus(302);
        }

        // 6e tentative en moins d'une minute → 429
        $this->post('/register', $this->invalidRegistrationData())
            ->assertStatus(429);
    }

    public function test_login_is_rate_limited_to_5_attempts_per_minute(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => 'unknown@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login');
        }

        $this->from('/login')->post('/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_verification_notification_is_rate_limited_to_6_attempts_per_minute(): void
    {
        $user = User::factory()->unverified()->create();

        // 6 requêtes autorisées (le throttle compte même si le contrôleur
        // renvoie une redirection)
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)
                ->post('/email/verification-notification')
                ->assertStatus(302);
        }

        // 7e requête → 429
        $this->actingAs($user)
            ->post('/email/verification-notification')
            ->assertStatus(429);
    }

    // ---------------------------------------------------------------
    // CSRF (natif Laravel)
    //
    // Note : Laravel désactive la vérification CSRF pendant les tests
    // (PreventRequestForgery::runningUnitTests()), il est donc impossible
    // d'obtenir un 419 ici. On vérifie à la place que :
    //   1. le middleware ValidateCsrfToken est bien enregistré sur les routes
    //   2. les formulaires rendent bien le champ _token (@csrf)
    // ---------------------------------------------------------------

    public function test_csrf_middleware_is_registered_on_register(): void
    {
        $this->assertContains(
            PreventRequestForgery::class,
            $this->webMiddlewareGroup(),
        );
    }

    public function test_csrf_middleware_is_registered_on_login(): void
    {
        $this->assertContains(
            PreventRequestForgery::class,
            $this->webMiddlewareGroup(),
        );
    }

    public function test_csrf_middleware_is_registered_on_verification_notification(): void
    {
        $this->assertContains(
            PreventRequestForgery::class,
            $this->webMiddlewareGroup(),
        );
    }

    public function test_register_form_contains_csrf_token(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_login_form_contains_csrf_token(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_verify_email_form_contains_csrf_token(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/email/verify')
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    // ---------------------------------------------------------------
    // Middleware 'guest'
    // ---------------------------------------------------------------

    public function test_authenticated_user_cannot_access_register(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/register')
            ->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_cannot_access_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect('/dashboard');
    }

    // ---------------------------------------------------------------
    // Middleware 'auth' + 'verified'
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Espace candidat')
            ->assertSee($user->name)
            ->assertSee('SMS disponibles');
    }

    public function test_admin_can_access_the_shared_dashboard_sidebar(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Espace administrateur')
            ->assertSee('Dashboard')
            ->assertSee('Offres')
            ->assertSee('Paramètres')
            ->assertSee('bi-speedometer2', false)
            ->assertSee('bi-box-arrow-right', false)
            ->assertSee('Déconnexion');
    }

    public function test_successful_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    // ---------------------------------------------------------------
    // Protection contre l'énumération d'emails
    // ---------------------------------------------------------------

    public function test_register_does_not_reveal_if_email_exists(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('email');

        // Message générique : ne révèle PAS que l'email est déjà pris
        $this->assertSame(
            'Ces identifiants sont déjà associés à un compte existant.',
            session('errors')->first('email'),
        );
    }

    public function test_register_does_not_reveal_if_phone_exists(): void
    {
        User::factory()->create(['phone' => '+261 34 12 345 67']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('phone');

        // Message générique : ne révèle PAS que le téléphone est déjà pris
        $this->assertSame(
            'Ces identifiants sont déjà associés à un compte existant.',
            session('errors')->first('phone'),
        );
    }

    public function test_login_does_not_reveal_if_email_exists(): void
    {
        $response = $this->post('/login', [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');

        // Message générique : ne révèle PAS que l'email est inconnu
        $this->assertSame(
            'Identifiants incorrects ou compte non disponible.',
            session('errors')->first('email'),
        );
    }

    public function test_login_does_not_reveal_inactive_or_unverified_account_status(): void
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

    // ---------------------------------------------------------------
    // Données de test
    // ---------------------------------------------------------------

    /**
     * Retourne les middlewares du groupe 'web' (configuration de l'application).
     *
     * @return array<int, string>
     */
    private function webMiddlewareGroup(): array
    {
        return app(Middleware::class)
            ->getMiddlewareGroups()['web'];
    }

    /**
     * Données d'inscription valides (pour les tests CSRF).
     *
     * @return array<string, string>
     */
    private function validRegistrationData(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test'.uniqid().'@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];
    }

    /**
     * Données d'inscription invalides (phone hors format Madagascar).
     *
     * @return array<string, string>
     */
    private function invalidRegistrationData(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test'.uniqid().'@example.com',
            'phone' => '12345',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];
    }
}
