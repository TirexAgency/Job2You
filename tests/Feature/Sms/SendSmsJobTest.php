<?php

namespace Tests\Feature\Sms;

use App\Exceptions\SmsProviderException;
use App\Jobs\SendSmsJob;
use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Sms\SmsProvider;
use App\Services\Sms\SmsQuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendSmsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.sms.url', 'https://sms.test/api/v1/send-sms');
        config()->set('services.sms.key', 'test-key');
        config()->set('services.sms.sender', 'Job2You');
    }

    public function test_it_sends_the_sms_and_records_the_provider_status(): void
    {
        Http::fake([
            'sms.test/*' => Http::response([
                'success' => true,
                'data' => ['id' => 'provider-42', 'status' => 'sent'],
            ]),
        ]);

        $user = User::factory()->create();
        $subscription = $this->freeSubscription($user);

        $job = new SendSmsJob($user->id, null, $user->phone, 'Bonjour', 'alert:1');
        $job->handle(app(SmsProvider::class), app(SmsQuotaService::class));

        $this->assertDatabaseHas('sms_logs', [
            'idempotency_key' => 'alert:1',
            'user_id' => $user->id,
            'status' => SmsLog::STATUS_SENT,
            'provider_reference' => 'provider-42',
            'provider_status' => 'sent',
            'quota_consumed' => true,
        ]);

        $this->assertSame(1, $subscription->fresh()->sms_remaining);
        Http::assertSentCount(1);
    }

    public function test_the_same_idempotency_key_never_sends_twice(): void
    {
        Http::fake([
            'sms.test/*' => Http::response(['success' => true]),
        ]);

        $user = User::factory()->create();
        $this->freeSubscription($user);

        $job = new SendSmsJob($user->id, null, $user->phone, 'Bonjour', 'alert:duplicate');
        $job->handle(app(SmsProvider::class), app(SmsQuotaService::class));
        $job->handle(app(SmsProvider::class), app(SmsQuotaService::class));

        $this->assertSame(1, SmsLog::where('idempotency_key', 'alert:duplicate')->count());
        Http::assertSentCount(1);
    }

    public function test_it_blocks_the_send_when_quota_is_exhausted(): void
    {
        Http::preventStrayRequests();

        $user = User::factory()->create();
        $this->freeSubscription($user, 0);

        $job = new SendSmsJob($user->id, null, $user->phone, 'Bonjour', 'alert:quota');
        $job->handle(app(SmsProvider::class), app(SmsQuotaService::class));

        $this->assertDatabaseHas('sms_logs', [
            'idempotency_key' => 'alert:quota',
            'status' => SmsLog::STATUS_FAILED,
            'error_message' => 'Quota SMS épuisé.',
            'quota_consumed' => false,
        ]);
    }

    public function test_terminal_failure_marks_the_log_failed_and_releases_the_quota(): void
    {
        Http::fake([
            'sms.test/*' => Http::response([
                'success' => false,
                'error' => ['message' => 'Service indisponible.'],
            ], 503),
        ]);

        $user = User::factory()->create();
        $subscription = $this->freeSubscription($user);

        $job = new SendSmsJob($user->id, null, $user->phone, 'Bonjour', 'alert:failed');

        try {
            $job->handle(app(SmsProvider::class), app(SmsQuotaService::class));
            $this->fail('Le job aurait dû lever une exception fournisseur.');
        } catch (SmsProviderException $exception) {
            $job->failed($exception);
        }

        $this->assertDatabaseHas('sms_logs', [
            'idempotency_key' => 'alert:failed',
            'status' => SmsLog::STATUS_FAILED,
            'quota_consumed' => false,
        ]);

        $this->assertSame(2, $subscription->fresh()->sms_remaining);
    }

    public function test_it_is_unique_per_idempotency_key_and_retries_with_backoff(): void
    {
        $job = new SendSmsJob(1, null, '+261340000000', 'Bonjour', 'alert:unique');

        $this->assertSame('alert:unique', $job->uniqueId());
        $this->assertSame(3, $job->tries);
        $this->assertSame([10, 60, 300], $job->backoff);
    }

    private function freeSubscription(User $user, int $remaining = 2): Subscription
    {
        $plan = Plan::create([
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);

        return $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => $remaining,
            'status' => 'active',
        ]);
    }
}
