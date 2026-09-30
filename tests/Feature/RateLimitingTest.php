<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Config::set('cache.default', 'array');
        Cache::setDefaultDriver('array');
        parent::tearDown();
    }

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
    }

    // ============================================================
    // Rate limiting : inscription (5/minute)
    // ============================================================

    public function test_registration_is_rate_limited_after_5_attempts(): void
    {
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');
        $this->seedPlans();

        // 5 tentatives invalides (rate limiting compte même les échecs)
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test'.$i.'@example.com',
                'phone' => 'invalid-phone',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
            ]);
        }

        // 6ème tentative => 429 Too Many Requests
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test6@example.com',
            'phone' => 'invalid-phone',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(429);
    }

    public function test_registration_allows_5_attempts_per_minute(): void
    {
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');
        $this->seedPlans();

        // 5 tentatives invalides => toutes doivent passer (422 validation, pas 429)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test'.$i.'@example.com',
                'phone' => 'invalid-phone',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
            ]);

            $response->assertStatus(302); // Redirect avec erreurs de validation
        }
    }

    // ============================================================
    // Rate limiting : connexion (5/minute)
    // ============================================================

    public function test_login_is_rate_limited_after_5_attempts(): void
    {
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');

        // 5 tentatives invalides
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // 6ème tentative => 429
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }

    // ============================================================
    // Rate limiting : renvoi email de vérification (6/minute)
    // ============================================================

    public function test_verification_notification_is_rate_limited(): void
    {
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');

        $user = User::factory()->unverified()->create();

        // 6 tentatives => toutes doivent passer
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)->post('/email/verification-notification');
        }

        // 7ème tentative => 429
        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertStatus(429);
    }

    // ============================================================
    // Rate limiting : vérification email (6/minute)
    // ============================================================

    public function test_email_verification_is_rate_limited(): void
    {
        Config::set('cache.default', 'database');
        Cache::setDefaultDriver('database');

        $user = User::factory()->unverified()->create();

        // 6 tentatives => toutes doivent passer
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)->get('/email/verify/'.$user->id.'/'.sha1($user->email));
        }

        // 7ème tentative => 429
        $response = $this->actingAs($user)->get('/email/verify/'.$user->id.'/'.sha1($user->email));

        $response->assertStatus(429);
    }
}
