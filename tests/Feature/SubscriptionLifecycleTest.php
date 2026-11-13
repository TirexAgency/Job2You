<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionActivated;
use App\Notifications\SubscriptionExpired;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_payment_activates_subscription_and_notifies_candidate(): void
    {
        Notification::fake();

        $freePlan = Plan::factory()->free()->create(['sms_quota' => 2]);
        $premiumPlan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);

        $user = User::factory()->create(['plan' => 'free', 'sms_quota' => 2]);
        $freeSubscription = $this->subscriptionFor($user, $freePlan, endsAt: null, smsRemaining: 2);

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $premiumPlan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'test-'.Str::uuid(),
            'amount' => $premiumPlan->price,
            'status' => 'pending',
            'payload_reference' => (string) Str::uuid(),
        ]);

        $response = $this->actingAs($user)->post(route('candidate.payment.sandbox.confirm', [
            'payment' => $payment,
            'outcome' => 'success',
        ]));

        $response->assertRedirect(route('candidate.subscription'));
        $response->assertSessionHas('status', 'plan-changed');

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $premiumPlan->id,
            'status' => 'active',
            'sms_remaining' => 50,
        ]);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $freeSubscription->id,
            'status' => 'inactive',
        ]);

        $subscription = $user->fresh()->activeSubscription();
        $this->assertNotNull($subscription);
        $this->assertNotNull($subscription->ends_at);
        $this->assertTrue($subscription->ends_at->isFuture());
        $this->assertSame('premium', $user->fresh()->plan);
        $this->assertSame(50, $user->fresh()->sms_quota);
        $this->assertSame(50, $subscription->sms_remaining);

        Notification::assertSentTo($user, SubscriptionActivated::class);
    }

    public function test_free_formula_can_be_activated_directly(): void
    {
        Notification::fake();

        $freePlan = Plan::factory()->free()->create(['sms_quota' => 3]);
        $user = User::factory()->create(['plan' => 'premium', 'sms_quota' => 50]);

        $response = $this->actingAs($user)->post(route('candidate.subscription.change'), [
            'plan_id' => $freePlan->id,
        ]);

        $response->assertRedirect(route('candidate.subscription'));
        $response->assertSessionHas('status', 'plan-changed');

        $subscription = $user->fresh()->activeSubscription();
        $this->assertNotNull($subscription);
        $this->assertSame($freePlan->id, $subscription->plan_id);
        $this->assertNull($subscription->ends_at);
        $this->assertSame('free', $user->fresh()->plan);
        $this->assertSame(3, $user->fresh()->sms_quota);

        Notification::assertSentTo($user, SubscriptionActivated::class);
    }

    public function test_paid_formula_requires_checkout_before_activation(): void
    {
        $paidPlan = Plan::factory()->create([
            'name' => 'pro',
            'price' => 49000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'active' => true,
        ]);
        $user = User::factory()->create(['plan' => 'free']);

        $response = $this->actingAs($user)->post(route('candidate.subscription.change'), [
            'plan_id' => $paidPlan->id,
        ]);

        $response->assertRedirect(route('candidate.checkout', $paidPlan));
        $response->assertSessionHas('info');
        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $paidPlan->id,
        ]);
    }

    public function test_expiration_command_downgrades_candidate_to_free_plan_and_notifies_once(): void
    {
        Notification::fake();

        $freePlan = Plan::factory()->free()->create(['sms_quota' => 2]);
        $premiumPlan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);

        $user = User::factory()->create(['plan' => 'premium', 'sms_quota' => 50]);
        $expiredSubscription = $this->subscriptionFor(
            $user,
            $premiumPlan,
            endsAt: now()->subDay(),
            smsRemaining: 12,
        );

        $this->artisan('subscriptions:expire')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $expiredSubscription->id,
            'status' => 'inactive',
        ]);

        $activeSubscription = $user->fresh()->activeSubscription();
        $this->assertNotNull($activeSubscription);
        $this->assertSame($freePlan->id, $activeSubscription->plan_id);
        $this->assertSame('free', $user->fresh()->plan);
        $this->assertSame(2, $user->fresh()->sms_quota);

        Notification::assertSentToTimes($user, SubscriptionExpired::class, 1);

        // Une seconde exécution ne doit pas rejouer la rétrogradation ni la notification.
        $this->artisan('subscriptions:expire')->assertSuccessful();

        Notification::assertSentToTimes($user, SubscriptionExpired::class, 1);
    }

    public function test_subscription_expiration_is_detected_on_next_candidate_request(): void
    {
        Notification::fake();

        $freePlan = Plan::factory()->free()->create(['sms_quota' => 2]);
        $premiumPlan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'active' => true,
        ]);

        $user = User::factory()->create(['plan' => 'premium', 'sms_quota' => 50]);
        $this->subscriptionFor($user, $premiumPlan, endsAt: now()->subMinute(), smsRemaining: 10);

        $this->actingAs($user)->get(route('candidate.subscription'))->assertOk();

        $this->assertSame('free', $user->fresh()->plan);
        $this->assertSame($freePlan->id, $user->fresh()->activeSubscription()?->plan_id);
        Notification::assertSentTo($user, SubscriptionExpired::class);
    }

    private function subscriptionFor(
        User $user,
        Plan $plan,
        ?\DateTimeInterface $endsAt = null,
        int $smsRemaining = 0,
    ): Subscription {
        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now()->subMonth(),
            'ends_at' => $endsAt,
            'sms_remaining' => $smsRemaining,
            'sms_reset_at' => null,
            'status' => 'active',
        ]);
    }
}
