<?php

namespace Tests\Feature\Auth;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Restaurer le driver de cache par défaut après chaque test
        Config::set('cache.default', 'array');
        Cache::setDefaultDriver('array');
        parent::tearDown();
    }

    // ============================================================
    // Test fonctionnel : inscription réussie
    // ============================================================

    public function test_successful_registration_creates_user(): void
    {
        $this->seedPlans();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'candidate',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_successful_registration_creates_subscription(): void
    {
        $this->seedPlans();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $subscription = $user->subscriptions()->first();
        $this->assertEquals(2, $subscription->sms_remaining);
        $this->assertEquals('free', $subscription->plan->name);
    }

    public function test_successful_registration_logs_user_in(): void
    {
        $this->seedPlans();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $this->assertAuthenticated();
    }

    public function test_successful_registration_dispatches_registered_event(): void
    {
        Event::fake();

        $this->seedPlans();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        Event::assertDispatched(Registered::class);
    }

    // ============================================================
    // Test fonctionnel : email de validation envoyé
    // ============================================================

    public function test_email_verification_notification_is_sent(): void
    {
        Notification::fake();

        $this->seedPlans();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    // ============================================================
    // Test fonctionnel : email dupliqué rejeté
    // ============================================================

    public function test_duplicate_email_is_rejected(): void
    {
        $this->seedPlans();

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'phone' => '+261 34 12 345 99',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_duplicate_phone_is_rejected(): void
    {
        $this->seedPlans();

        User::factory()->create(['phone' => '+261 34 12 345 67']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    // ============================================================
    // Test fonctionnel : rate limiting
    // ============================================================

    public function test_register_is_rate_limited_after_5_attempts(): void
    {
        // Le cache 'array' ne persiste pas entre requêtes en test.
        // On utilise 'database' pour que le rate limiting fonctionne.
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');
        $this->seedPlans();

        // Données volontairement invalides : l'inscription échoue, personne
        // n'est connecté, et le middleware 'guest' ne redirige pas.
        // Le middleware 'throttle' compte donc chaque tentative.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test'.$i.'@example.com',
                'phone' => 'invalid-phone',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
            ]);
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test6@example.com',
            'phone' => 'invalid-phone',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(429);
    }

    // ============================================================
    // Test fonctionnel : validation email avec token valide/invalide
    // ============================================================

    public function test_email_can_be_verified_with_valid_token(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_cannot_be_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_cannot_be_verified_with_unsigned_url(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/email/verify/'.$user->id.'/'.sha1($user->email));

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_cannot_be_verified_with_expired_token(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    // ============================================================
    // Test fonctionnel : renvoi email
    // ============================================================

    public function test_verification_email_can_be_resent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertSessionHas('resent');
    }

    public function test_verification_email_is_actually_resent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_email_not_resent_if_already_verified(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect(route('home'));
        Notification::assertNothingSent();
    }

    public function test_verification_notification_is_rate_limited(): void
    {
        // Le cache 'array' ne persiste pas entre requêtes en test.
        // On utilise 'database' pour que le rate limiting fonctionne.
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');

        $user = User::factory()->unverified()->create();

        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)->post('/email/verification-notification');
        }

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertStatus(429);
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function seedPlans(): void
    {
        Plan::create([
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);

        Plan::create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
    }
}
