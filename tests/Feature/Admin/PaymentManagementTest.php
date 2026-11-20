<?php

namespace Tests\Feature\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_payments_with_statuses_and_filters(): void
    {
        $paid = $this->payment([
            'provider_reference' => 'pay-paid-ref',
            'payload_reference' => 'J2Y-PAID-REF',
            'status' => Payment::STATUS_PAID,
            'amount' => 25000,
        ]);
        $this->payment([
            'provider_reference' => 'pay-failed-ref',
            'payload_reference' => 'J2Y-FAILED-REF',
            'status' => Payment::STATUS_FAILED,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/payments?status=paid');

        $response->assertOk();
        $response->assertViewIs('admin.payments.index');
        $response->assertSee('J2Y-PAID-REF');
        $response->assertDontSee('J2Y-FAILED-REF');
        $response->assertSee('Montant encaissé');
        $response->assertSee('25 000 Ar');
        $response->assertViewHas('stats', fn (array $stats): bool => $stats['paid'] === 1 && $stats['failed'] === 1);
        $this->assertTrue($paid->isPaid());
    }

    public function test_admin_can_refund_a_paid_payment(): void
    {
        $payment = $this->payment([
            'provider_reference' => 'pay-refund-ref',
            'status' => Payment::STATUS_PAID,
        ]);

        $response = $this->actingAs($this->admin)
            ->from('/admin/payments')
            ->patch(route('admin.payments.refund', $payment));

        $response->assertRedirect('/admin/payments');
        $response->assertSessionHas('success', 'Le paiement a été marqué comme remboursé.');

        $payment->refresh();
        $this->assertSame(Payment::STATUS_REFUNDED, $payment->status);
        $this->assertNotNull($payment->refunded_at);
    }

    public function test_pending_payment_cannot_be_refunded(): void
    {
        $payment = $this->payment([
            'provider_reference' => 'pay-pending-ref',
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)
            ->from('/admin/payments')
            ->patch(route('admin.payments.refund', $payment));

        $response->assertRedirect('/admin/payments');
        $response->assertSessionHas('error', 'Seuls les paiements confirmés peuvent être remboursés.');
        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
    }

    public function test_non_admin_cannot_refund_payment(): void
    {
        $payment = $this->payment([
            'provider_reference' => 'pay-forbidden-ref',
            'status' => Payment::STATUS_PAID,
        ]);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/payments')->assertForbidden();
        $this->actingAs($candidate)->patch(route('admin.payments.refund', $payment))->assertForbidden();
        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function payment(array $attributes = []): Payment
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create();

        return Payment::create(array_merge([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'ref-'.fake()->unique()->uuid(),
            'amount' => 15000,
            'status' => Payment::STATUS_PENDING,
            'payload_reference' => 'J2Y-'.fake()->unique()->uuid(),
        ], $attributes));
    }
}
