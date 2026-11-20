<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FiveOnePayCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_fiveonepay_payment_and_redirects_to_hosted_page(): void
    {
        config([
            'services.fiveonepay.base_url' => 'https://api.fiveonepay.com/v1',
            'services.fiveonepay.secret' => 'sk_test_fake',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'api.fiveonepay.com/v1/payments' => Http::response([
                'id' => 'clzx8p2k9000108l0hf2v9m3d',
                'status' => 'PENDING',
                'environment' => 'SANDBOX',
                'amount' => 15000,
                'payable_amount' => 15000,
                'currency' => 'MGA',
                'reference' => 'ignored-by-local-record',
                'fiveonepay_reference' => '48205173',
                'operator' => 'MVOLA',
                'payment_url' => 'https://pay.fiveonepay.com/pay/48205173',
                'expires_at' => now()->addMinutes(30)->toIso8601String(),
            ], 201),
        ]);

        $plan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'active' => true,
        ]);
        $user = User::factory()->create(['phone' => '0341234567']);

        $response = $this->actingAs($user)->post(route('candidate.checkout', $plan));

        $response->assertRedirect('https://pay.fiveonepay.com/pay/48205173');

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('clzx8p2k9000108l0hf2v9m3d', $payment->provider_reference);
        $this->assertSame('https://pay.fiveonepay.com/pay/48205173', $payment->payment_url);
        $this->assertSame('MVOLA', $payment->operator);
        $this->assertNotNull($payment->expires_at);
        $this->assertSame(0, Subscription::query()->count());

        Http::assertSent(function (ClientRequest $request) use ($payment): bool {
            return $request->url() === 'https://api.fiveonepay.com/v1/payments'
                && $request['amount'] === 15000
                && $request['reference'] === $payment->payload_reference
                && $request['operator'] === 'MVOLA'
                && $request['payerNumber'] === '0341234567'
                && $request->hasHeader('Idempotency-Key', $payment->payload_reference)
                && str_contains((string) $request['callbackUrl'], '/webhooks/fiveonepay')
                && str_contains((string) $request['successUrl'], '/candidate/payment/'.$payment->id.'/return');
        });
    }

    public function test_checkout_reuses_an_existing_pending_payment(): void
    {
        config([
            'services.fiveonepay.base_url' => 'https://api.fiveonepay.com/v1',
            'services.fiveonepay.secret' => 'sk_test_fake',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'api.fiveonepay.com/v1/payments' => Http::sequence()
                ->push([
                    'id' => 'clzx8p2k9000108l0hf2v9m3d',
                    'status' => 'PENDING',
                    'amount' => 15000,
                    'operator' => 'MVOLA',
                    'payment_url' => 'https://pay.fiveonepay.com/pay/48205173',
                    'expires_at' => now()->addMinutes(30)->toIso8601String(),
                ], 201),
        ]);

        $plan = Plan::factory()->create(['price' => 15000, 'active' => true]);
        $user = User::factory()->create(['phone' => '0341234567']);

        $this->actingAs($user)->post(route('candidate.checkout', $plan))
            ->assertRedirect('https://pay.fiveonepay.com/pay/48205173');
        $this->actingAs($user)->post(route('candidate.checkout', $plan))
            ->assertRedirect('https://pay.fiveonepay.com/pay/48205173');

        $this->assertSame(1, Payment::query()->count());
        Http::assertSentCount(1);
    }

    public function test_checkout_marks_payment_failed_when_fiveonepay_rejects_the_request(): void
    {
        config([
            'services.fiveonepay.base_url' => 'https://api.fiveonepay.com/v1',
            'services.fiveonepay.secret' => 'sk_test_fake',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'api.fiveonepay.com/v1/payments' => Http::response([
                'statusCode' => 400,
                'message' => 'Solde maximum atteint.',
                'error' => 'Bad Request',
            ], 400),
        ]);

        $plan = Plan::factory()->create(['price' => 15000, 'active' => true]);
        $user = User::factory()->create(['phone' => '0341234567']);

        $this->actingAs($user)->post(route('candidate.checkout', $plan))
            ->assertRedirect(route('candidate.subscription'))
            ->assertSessionHas('error', 'Solde maximum atteint.');

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::STATUS_FAILED, $payment->status);
        $this->assertSame('Solde maximum atteint.', $payment->error_message);
        $this->assertSame(0, Subscription::query()->count());
    }

    public function test_paid_checkout_without_provider_configuration_uses_local_simulation_in_testing(): void
    {
        config([
            'services.fiveonepay.secret' => '',
        ]);

        $plan = Plan::factory()->create(['price' => 15000, 'active' => true]);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.checkout', $plan))
            ->assertRedirect();

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertStringStartsWith('sandbox-', $payment->provider_reference);
    }

    public function test_subscription_page_displays_pending_payment_link(): void
    {
        $plan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'active' => true,
        ]);
        $user = User::factory()->create();

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'clzx8p2k9000108l0hf2v9m3d',
            'amount' => $plan->price,
            'status' => Payment::STATUS_PENDING,
            'payment_url' => 'https://pay.fiveonepay.com/pay/48205173',
            'expires_at' => now()->addMinutes(30),
            'payload_reference' => 'J2Y-TEST-PENDING',
        ]);

        $this->actingAs($user)
            ->get(route('candidate.subscription'))
            ->assertOk()
            ->assertSee('Paiement en attente')
            ->assertSee('Reprendre le paiement')
            ->assertSee('J2Y-TEST-PENDING')
            ->assertSee($payment->payment_url);
    }

    public function test_return_from_provider_synchronizes_payment_without_webhook(): void
    {
        Notification::fake();

        config([
            'services.fiveonepay.base_url' => 'https://api.fiveonepay.com/v1',
            'services.fiveonepay.secret' => 'sk_test_fake',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'api.fiveonepay.com/v1/payments/clzx8p2k9000108l0hf2v9m3d' => Http::response([
                'id' => 'clzx8p2k9000108l0hf2v9m3d',
                'status' => 'SUCCESS',
                'amount' => 15000,
                'currency' => 'MGA',
                'reference' => 'J2Y-TEST-RETURN',
                'operator' => 'MVOLA',
                'paid_at' => now()->toIso8601String(),
            ], 200),
        ]);

        $plan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'active' => true,
        ]);
        $user = User::factory()->create();
        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'clzx8p2k9000108l0hf2v9m3d',
            'amount' => $plan->price,
            'status' => Payment::STATUS_PENDING,
            'payload_reference' => 'J2Y-TEST-RETURN',
        ]);

        $this->actingAs($user)
            ->get(route('candidate.payment.return', ['payment' => $payment, 'outcome' => 'success']))
            ->assertRedirect(route('candidate.subscription'));

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame($plan->id, $user->fresh()->activeSubscription()?->plan_id);
    }
}
