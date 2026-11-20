<?php

namespace Tests\Feature\Admin;

use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_sees_sms_history_with_quotas_and_statuses(): void
    {
        [$user, $subscription] = $this->subscribedUser(quota: 10, remaining: 3);

        SmsLog::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'quota_source' => 'subscription',
            'recipient' => '0341234567',
            'message' => 'Alerte offre développeur',
            'status' => SmsLog::STATUS_SENT,
            'quota_consumed' => true,
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/sms');

        $response->assertOk();
        $response->assertViewIs('admin.sms.index');
        $response->assertSee('Historique SMS');
        $response->assertSee('Alerte offre développeur');
        $response->assertSee('3 / 10');
        $response->assertSee('Quota restant');
        $response->assertViewHas('stats', fn (array $stats): bool => $stats['sent_month'] === 1
            && $stats['quota_consumed'] === 1
            && $stats['remaining_quota'] === 3);
    }

    public function test_admin_can_filter_sms_by_status(): void
    {
        $user = User::factory()->create(['sms_quota' => 2, 'sms_sent' => 1]);

        SmsLog::create([
            'user_id' => $user->id,
            'quota_source' => 'free',
            'recipient' => '0341234567',
            'message' => 'Message envoyé unique',
            'status' => SmsLog::STATUS_SENT,
            'quota_consumed' => true,
            'sent_at' => now(),
        ]);

        SmsLog::create([
            'user_id' => $user->id,
            'quota_source' => 'free',
            'recipient' => '0341234567',
            'message' => 'Message échoué unique',
            'status' => SmsLog::STATUS_FAILED,
            'quota_consumed' => false,
            'error_message' => 'Numéro invalide',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/sms?status=failed&search=échoué');

        $response->assertOk();
        $response->assertSee('Message échoué unique');
        $response->assertDontSee('Message envoyé unique');
        $response->assertSee('1 / 2');
        $response->assertSee('Numéro invalide');
    }

    public function test_non_admin_cannot_access_sms_history(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/sms')->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Subscription}
     */
    private function subscribedUser(int $quota, int $remaining): array
    {
        $plan = Plan::factory()->create(['sms_quota' => $quota]);
        $user = User::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'sms_remaining' => $remaining,
            'status' => 'active',
        ]);

        return [$user, $subscription];
    }
}
