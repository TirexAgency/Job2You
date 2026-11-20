<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class SubscriptionNotificationFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_activation_succeeds_when_mail_transport_fails(): void
    {
        $plan = Plan::factory()->free()->create(['sms_quota' => 2]);
        $user = User::factory()->create();

        $this->mock(Dispatcher::class, function (MockInterface $mock): void {
            $mock->shouldReceive('send')->andThrow(new RuntimeException('SMTP unreachable'));
        });

        $subscription = app(SubscriptionService::class)->activate($user, $plan);

        $this->assertSame('active', $subscription->status);
        $this->assertSame($plan->id, $user->fresh()->activeSubscription()?->plan_id);
    }
}
