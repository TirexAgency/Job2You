<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionActivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FiveOnePayWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_success_webhook_activates_subscription_and_marks_payment_paid(): void
    {
        Notification::fake();

        [$user, $plan, $payment] = $this->pendingPayment();

        $response = $this->postWebhook($this->successPayload($payment), 'evt_success_1');

        $response->assertOk()->assertJson(['received' => true]);

        $payment->refresh();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame('SUCCESS', $payment->payload['status'] ?? null);

        $subscription = $user->fresh()->activeSubscription();
        $this->assertNotNull($subscription);
        $this->assertSame($plan->id, $subscription->plan_id);
        $this->assertSame('active', $subscription->status);

        Notification::assertSentToTimes($user, SubscriptionActivated::class, 1);
    }

    public function test_duplicate_webhook_event_is_ignored(): void
    {
        Notification::fake();

        [$user, , $payment] = $this->pendingPayment();
        $payload = $this->successPayload($payment);

        $this->postWebhook($payload, 'evt_duplicate')->assertOk();
        $this->postWebhook($payload, 'evt_duplicate')
            ->assertOk()
            ->assertJson(['received' => true, 'duplicate' => true]);

        $this->assertSame(1, Subscription::query()->where('user_id', $user->id)->count());
        Notification::assertSentToTimes($user, SubscriptionActivated::class, 1);
    }

    public function test_invalid_signature_is_rejected_without_activating_subscription(): void
    {
        Notification::fake();

        [$user, , $payment] = $this->pendingPayment();

        $this->postWebhook($this->successPayload($payment), 'evt_invalid', 'wrong-secret')
            ->assertStatus(400);

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertSame(0, Subscription::query()->where('user_id', $user->id)->count());
        Notification::assertNothingSent();
    }

    public function test_expired_webhook_then_late_success_still_activates_subscription(): void
    {
        Notification::fake();

        [$user, , $payment] = $this->pendingPayment();

        $this->postWebhook([
            'id' => 'evt_expired',
            'event' => 'payment.expired',
            'created_at' => now()->toIso8601String(),
            'data' => [
                'id' => $payment->provider_reference,
                'status' => 'EXPIRED',
                'amount' => (int) round((float) $payment->amount),
                'reference' => $payment->payload_reference,
            ],
        ], 'evt_expired')->assertOk();

        $this->assertSame(Payment::STATUS_FAILED, $payment->fresh()->status);
        $this->assertSame(0, Subscription::query()->where('user_id', $user->id)->count());

        $this->postWebhook($this->successPayload($payment), 'evt_late_success')->assertOk();

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertNotNull($user->fresh()->activeSubscription());
        Notification::assertSentToTimes($user, SubscriptionActivated::class, 1);
    }

    public function test_webhook_with_mismatched_amount_does_not_activate_subscription(): void
    {
        Notification::fake();

        [$user, , $payment] = $this->pendingPayment();
        $payload = $this->successPayload($payment);
        $payload['data']['amount'] = 1;

        $this->postWebhook($payload, 'evt_bad_amount')->assertOk();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertSame(0, Subscription::query()->where('user_id', $user->id)->count());
        Notification::assertNothingSent();
    }

    public function test_webhook_finds_payment_by_merchant_reference_when_provider_id_differs(): void
    {
        Notification::fake();

        [$user, $plan, $payment] = $this->pendingPayment();
        $payload = $this->successPayload($payment);
        $payload['data']['id'] = 'clz_other_identifier';

        $this->postWebhook($payload, 'evt_reference_lookup')->assertOk();

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame('clz_other_identifier', $payment->fresh()->provider_reference);
        $this->assertSame($plan->id, $user->fresh()->activeSubscription()?->plan_id);
    }

    /**
     * @return array{0: User, 1: Plan, 2: Payment}
     */
    private function pendingPayment(): array
    {
        $plan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
        $user = User::factory()->create(['plan' => 'free', 'sms_quota' => 2]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'clzx8p2k9000108l0hf2v9m3d',
            'amount' => $plan->price,
            'status' => Payment::STATUS_PENDING,
            'payload_reference' => 'J2Y-TEST-'.fake()->uuid(),
        ]);

        return [$user, $plan, $payment];
    }

    /**
     * @return array<string, mixed>
     */
    private function successPayload(Payment $payment): array
    {
        return [
            'id' => 'evt_'.fake()->uuid(),
            'event' => 'payment.success',
            'created_at' => now()->toIso8601String(),
            'data' => [
                'id' => $payment->provider_reference,
                'status' => 'SUCCESS',
                'amount' => (int) round((float) $payment->amount),
                'currency' => 'MGA',
                'reference' => $payment->payload_reference,
                'fiveonepay_reference' => '48205173',
                'operator' => 'MVOLA',
                'payer_number' => '0341234567',
                'paid_at' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postWebhook(array $payload, string $eventId, string $secret = 'whsec_test'): TestResponse
    {
        config(['services.fiveonepay.webhook_secret' => 'whsec_test']);

        $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', $rawBody, $secret);

        return $this->call(
            'POST',
            route('webhooks.fiveonepay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_FIVEONE_SIGNATURE' => $signature,
                'HTTP_X_FIVEONE_EVENT_ID' => $eventId,
            ],
            $rawBody,
        );
    }
}
