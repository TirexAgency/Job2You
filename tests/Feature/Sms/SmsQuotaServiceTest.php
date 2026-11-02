<?php

namespace Tests\Feature\Sms;

use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Sms\SmsQuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SmsQuotaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_can_send_two_sms_then_is_blocked(): void
    {
        $user = User::factory()->create([
            'sms_quota' => 2,
            'sms_sent' => 0,
        ]);

        $service = app(SmsQuotaService::class);

        $this->assertSame(2, $service->remaining($user));
        $this->assertSame(2, $service->quotaLimit($user));

        $this->assertTrue($service->reserve($user, $this->logFor($user)));
        $this->assertTrue($service->reserve($user, $this->logFor($user)));
        $this->assertFalse($service->reserve($user, $this->logFor($user)));

        $this->assertSame(2, $user->fresh()->sms_sent);
        $this->assertSame(0, $service->remaining($user->fresh()));
    }

    public function test_free_subscription_does_not_reset_monthly(): void
    {
        $plan = $this->createPlan('free', 0, 2);
        $user = User::factory()->create();

        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now()->subMonths(2),
            'ends_at' => null,
            'sms_remaining' => 0,
            'sms_reset_at' => now()->subMonths(2),
            'status' => 'active',
        ]);

        $service = app(SmsQuotaService::class);

        $this->assertSame(0, $service->remaining($user));
        $this->assertFalse($service->reserve($user, $this->logFor($user)));
        $this->assertSame(0, $subscription->fresh()->sms_remaining);
    }

    public function test_paid_subscription_quota_resets_after_one_month(): void
    {
        $plan = $this->createPlan('premium', 15000, 50);
        $user = User::factory()->create();

        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now()->subMonth()->subDay(),
            'ends_at' => now()->addMonths(6),
            'sms_remaining' => 0,
            'sms_reset_at' => now()->subMonth()->subDay(),
            'status' => 'active',
        ]);

        $service = app(SmsQuotaService::class);

        $this->assertSame(50, $service->remaining($user));
        $this->assertSame(50, $subscription->fresh()->sms_remaining);
        $this->assertNotNull($subscription->fresh()->sms_reset_at);

        $this->assertTrue($service->reserve($user, $this->logFor($user)));
        $this->assertSame(49, $subscription->fresh()->sms_remaining);
    }

    public function test_release_restores_a_reserved_free_quota(): void
    {
        $user = User::factory()->create([
            'sms_quota' => 2,
            'sms_sent' => 0,
        ]);

        $service = app(SmsQuotaService::class);
        $log = $this->logFor($user);

        $this->assertTrue($service->reserve($user, $log));
        $this->assertSame(1, $user->fresh()->sms_sent);
        $this->assertTrue($log->fresh()->quota_consumed);

        $service->release($log);

        $this->assertSame(0, $user->fresh()->sms_sent);
        $this->assertFalse($log->fresh()->quota_consumed);
    }

    public function test_reset_expired_quotas_only_resets_paid_subscriptions(): void
    {
        $freePlan = $this->createPlan('free', 0, 2);
        $premiumPlan = $this->createPlan('premium', 15000, 50);

        $freeUser = User::factory()->create();
        $premiumUser = User::factory()->create();

        $freeSubscription = $freeUser->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'starts_at' => now()->subMonths(2),
            'ends_at' => null,
            'sms_remaining' => 0,
            'sms_reset_at' => now()->subMonths(2),
            'status' => 'active',
        ]);

        $premiumSubscription = $premiumUser->subscriptions()->create([
            'plan_id' => $premiumPlan->id,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->addMonths(6),
            'sms_remaining' => 0,
            'sms_reset_at' => now()->subMonths(2),
            'status' => 'active',
        ]);

        $count = app(SmsQuotaService::class)->resetExpiredQuotas();

        $this->assertSame(1, $count);
        $this->assertSame(0, $freeSubscription->fresh()->sms_remaining);
        $this->assertSame(50, $premiumSubscription->fresh()->sms_remaining);
    }

    private function createPlan(string $name, float $price, int $smsQuota): Plan
    {
        return Plan::create([
            'name' => $name,
            'price' => $price,
            'duration_days' => $price > 0 ? 30 : 0,
            'sms_quota' => $smsQuota,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);
    }

    private function logFor(User $user): SmsLog
    {
        return SmsLog::create([
            'user_id' => $user->id,
            'offer_id' => null,
            'subscription_id' => null,
            'recipient' => $user->phone,
            'message' => 'Message de test',
            'idempotency_key' => 'test:'.Str::uuid(),
            'status' => SmsLog::STATUS_PENDING,
        ]);
    }
}
