<?php

namespace Tests\Feature\Auth;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SubscriptionAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ============================================================
    // ÉTAPE 6 — Attribution crédit SMS gratuit (RG01)
    // ============================================================

    public function test_user_has_free_subscription_after_registration(): void
    {
        $this->seedPlans();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasActiveSubscription());
    }

    public function test_free_subscription_has_correct_values(): void
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
        $subscription = $user->subscriptions()->first();

        $this->assertNotNull($subscription);
        $this->assertEquals('free', $subscription->plan->name);
        $this->assertEquals(2, $subscription->sms_remaining);
        $this->assertEquals('active', $subscription->status);
        $this->assertNull($subscription->ends_at);
        $this->assertNotNull($subscription->starts_at);
    }

    public function test_get_sms_quota_returns_correct_value(): void
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

        $this->assertEquals(2, $user->getSmsQuota());
    }

    public function test_has_active_subscription_returns_false_without_subscription(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasActiveSubscription());
    }

    public function test_has_active_subscription_returns_false_for_expired_subscription(): void
    {
        $this->seedPlans();

        $user = User::factory()->create();
        $freePlan = Plan::where('name', 'free')->first();

        $user->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDays(1),
            'sms_remaining' => 0,
            'status' => 'active',
        ]);

        $this->assertFalse($user->hasActiveSubscription());
    }

    public function test_has_active_subscription_returns_false_for_inactive_status(): void
    {
        $this->seedPlans();

        $user = User::factory()->create();
        $freePlan = Plan::where('name', 'free')->first();

        $user->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => 2,
            'status' => 'inactive',
        ]);

        $this->assertFalse($user->hasActiveSubscription());
    }

    public function test_get_sms_quota_returns_zero_without_active_subscription(): void
    {
        $user = User::factory()->create(['sms_quota' => 0]);

        $this->assertEquals(0, $user->getSmsQuota());
    }

    // ============================================================
    // ÉTAPE 7 — Rate limiting + CSRF + middleware
    // ============================================================

    public function test_register_route_has_rate_limiting(): void
    {
        $route = Route::getRoutes()->match(
            Request::create('/register', 'POST')
        );

        $this->assertContains('throttle:5,1', $route->middleware());
    }

    public function test_verification_notification_route_has_rate_limiting(): void
    {
        $route = Route::getRoutes()->match(
            Request::create('/email/verification-notification', 'POST')
        );

        $this->assertContains('throttle:6,1', $route->middleware());
    }

    public function test_csrf_protection_on_register(): void
    {
        $this->seedPlans();

        // Le formulaire d'inscription doit contenir un token CSRF
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('name="_token"', false);
    }

    public function test_guest_middleware_redirects_authenticated_user_from_register(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/dashboard');
    }

    public function test_auth_middleware_redirects_guest_from_profile(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_verified_middleware_redirects_unverified_user(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_profile(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    public function test_email_enumeration_protection_generic_message(): void
    {
        $this->seedPlans();

        // Créer un utilisateur avec cet email
        User::factory()->create(['email' => 'existing@example.com']);

        // Tenter de s'inscrire avec le même email
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'phone' => '+261 34 12 345 99',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        // Le message doit être générique (pas "email déjà utilisé")
        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Ces identifiants sont déjà associés à un compte existant.', $errors[0]);
    }

    public function test_phone_enumeration_protection_generic_message(): void
    {
        $this->seedPlans();

        // Créer un utilisateur avec ce numéro
        User::factory()->create(['phone' => '+261 34 12 345 67']);

        // Tenter de s'inscrire avec le même numéro
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'phone' => '+261 34 12 345 67',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        // Le message doit être générique (pas "numéro déjà utilisé")
        $response->assertSessionHasErrors('phone');
        $errors = session('errors')->get('phone');
        $this->assertStringContainsString('Ces identifiants sont déjà associés à un compte existant.', $errors[0]);
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
