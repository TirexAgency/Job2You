<?php

namespace Tests\Feature\Sms;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateSmsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_quota_limit_and_sms_history(): void
    {
        $user = User::factory()->create([
            'role' => 'candidate',
            'sms_quota' => 2,
            'sms_sent' => 2,
        ]);

        SmsLog::create([
            'user_id' => $user->id,
            'offer_id' => null,
            'subscription_id' => null,
            'recipient' => $user->phone,
            'message' => 'Une offre correspond à votre profil.',
            'idempotency_key' => 'page-test-1',
            'status' => SmsLog::STATUS_FAILED,
            'error_message' => 'Quota SMS épuisé.',
        ]);

        $response = $this->actingAs($user)->get(route('candidate.sms'));

        $response->assertOk();
        $response->assertSee('0 / 2 SMS');
        $response->assertSee('Quota SMS épuisé.');
        $response->assertSee('Une offre correspond à votre profil.');
    }
}
