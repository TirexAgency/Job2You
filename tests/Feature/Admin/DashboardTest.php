<?php

namespace Tests\Feature\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_sees_kpis_and_charts_with_fresh_data(): void
    {
        $plan = Plan::factory()->create(['price' => 25000]);
        $user = User::factory()->create();

        Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'dash-paid-1',
            'amount' => 25000,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'payload_reference' => 'J2Y-DASH-1',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Revenus confirmés');
        $response->assertSee('Statuts des paiements');
        $response->assertSee('Données au');
        $response->assertSee('25 000 Ar');

        $response->assertViewHas('charts', function (array $charts): bool {
            return count($charts['revenue']) === 6
                && count($charts['users']) === 6
                && count($charts['offers']) === 6
                && $charts['payment_status'][0]['value'] === 1;
        });

        $response->assertViewHas('stats', fn (array $stats): bool => $stats['payments']['paid'] === 1
            && (float) $stats['payments']['revenue_total'] === 25000.0);
    }

    public function test_revenue_chart_contains_the_current_month_amount(): void
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create();

        Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'dash-paid-2',
            'amount' => 15000,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'payload_reference' => 'J2Y-DASH-2',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertViewHas('charts', function (array $charts): bool {
            $currentMonth = $charts['revenue'][count($charts['revenue']) - 1];

            return (float) $currentMonth['value'] === 15000.0;
        });
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }
}
