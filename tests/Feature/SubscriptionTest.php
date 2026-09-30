<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Plan::create([
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);
    }

    private function createUserWithSubscription(): User
    {
        $user = User::factory()->create();

        $freePlan = Plan::where('name', 'free')->first();

        $user->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => $freePlan->sms_quota,
            'status' => 'active',
        ]);

        return $user;
    }

    public function test_user_has_free_subscription_after_registration(): void
    {
        $user = $this->createUserWithSubscription();

        $this->assertTrue($user->hasActiveSubscription());
    }

    public function test_free_plan_has_sms_quota_of_2(): void
    {
        $freePlan = Plan::where('name', 'free')->first();

        $this->assertNotNull($freePlan);
        $this->assertEquals(2, $freePlan->sms_quota);
    }

    public function test_user_sms_quota_is_2_for_free_plan(): void
    {
        $user = $this->createUserWithSubscription();

        $this->assertEquals(2, $user->getSmsQuota());
    }

    public function test_user_without_active_subscription_has_base_quota(): void
    {
        $user = User::factory()->create([
            'sms_quota' => 5,
        ]);

        $this->assertEquals(5, $user->getSmsQuota());
    }

    public function test_subscription_is_created_with_correct_status(): void
    {
        $user = $this->createUserWithSubscription();

        $subscription = $user->subscriptions()->first();

        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertNull($subscription->ends_at);
        $this->assertEquals(2, $subscription->sms_remaining);
    }

    public function test_user_has_free_sms_remaining(): void
    {
        $user = $this->createUserWithSubscription();

        $this->assertTrue($user->hasFreeSmsRemaining());
    }

    public function test_user_without_free_sms_remaining(): void
    {
        $user = User::factory()->create([
            'sms_sent' => 2,
        ]);

        $this->assertFalse($user->hasFreeSmsRemaining());
    }
}
