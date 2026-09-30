<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crée le plan gratuit utilisé par les abonnements de test.
     */
    private function createFreePlan(): Plan
    {
        return Plan::create([
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);
    }

    // ============================================================
    // Test unitaire : mot de passe hashé
    // ============================================================

    public function test_password_is_hashed_on_creation(): void
    {
        $user = User::factory()->create([
            'password' => 'Password1!',
        ]);

        $this->assertNotEquals('Password1!', $user->password);
        $this->assertTrue(Hash::check('Password1!', $user->password));
    }

    public function test_password_is_hashed_on_update(): void
    {
        $user = User::factory()->create();

        $user->update(['password' => 'NewPassword1!']);

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_password_hash_uses_bcrypt(): void
    {
        $user = User::factory()->create([
            'password' => 'Password1!',
        ]);

        $this->assertStringStartsWith('$2y$', $user->password);
    }

    // ============================================================
    // Test unitaire : unicité email
    // ============================================================

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['email' => 'test@example.com']);
    }

    public function test_phone_must_be_unique(): void
    {
        User::factory()->create(['phone' => '+261 34 12 345 67']);

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['phone' => '+261 34 12 345 67']);
    }

    // ============================================================
    // Test unitaire : hasActiveSubscription
    // ============================================================

    public function test_has_active_subscription_returns_true_for_active_subscription(): void
    {
        $plan = $this->createFreePlan();
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => 2,
            'status' => 'active',
        ]);

        $this->assertTrue($user->hasActiveSubscription());
    }

    public function test_has_active_subscription_returns_false_without_subscription(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasActiveSubscription());
    }

    // ============================================================
    // Test unitaire : getSmsQuota
    // ============================================================

    public function test_get_sms_quota_returns_subscription_quota(): void
    {
        $plan = $this->createFreePlan();
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => 5,
            'status' => 'active',
        ]);

        $this->assertEquals(5, $user->getSmsQuota());
    }

    public function test_get_sms_quota_returns_zero_without_active_subscription(): void
    {
        $user = User::factory()->create(['sms_quota' => 0]);

        $this->assertEquals(0, $user->getSmsQuota());
    }
}
