<?php

namespace Tests\Feature\Sms;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsResetQuotasCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resets_the_monthly_quota_of_a_paid_subscription(): void
    {
        $plan = Plan::create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);

        $user = User::factory()->create();

        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now()->subMonth()->subDay(),
            'ends_at' => now()->addMonths(6),
            'sms_remaining' => 0,
            'sms_reset_at' => now()->subMonth()->subDay(),
            'status' => 'active',
        ]);

        $this->artisan('sms:reset-quotas')
            ->expectsOutputToContain('Quotas SMS réinitialisés : 1 abonnement(s).')
            ->assertSuccessful();

        $this->assertSame(50, $subscription->fresh()->sms_remaining);
    }
}
