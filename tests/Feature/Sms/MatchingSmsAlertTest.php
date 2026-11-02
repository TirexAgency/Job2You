<?php

namespace Tests\Feature\Sms;

use App\Jobs\SendSmsJob;
use App\Models\CandidateProfile;
use App\Models\Offer;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MatchingSmsAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_matching_a_new_offer_dispatches_an_sms_alert(): void
    {
        Queue::fake();

        [$user, $profile] = $this->candidate(alertsEnabled: true);
        $offer = $this->matchingOffer();

        app(MatchingService::class)->computeForOffer($offer);

        Queue::assertPushed(SendSmsJob::class, function (SendSmsJob $job) use ($user, $offer): bool {
            return $job->userId === $user->id
                && $job->offerId === $offer->id
                && $job->recipient === $user->phone
                && $job->idempotencyKey === "offer-alert:{$user->id}:{$offer->id}";
        });
    }

    public function test_no_alert_is_dispatched_when_alerts_are_disabled(): void
    {
        Queue::fake();

        $this->candidate(alertsEnabled: false);
        $offer = $this->matchingOffer();

        app(MatchingService::class)->computeForOffer($offer);

        Queue::assertNotPushed(SendSmsJob::class);
    }

    public function test_no_alert_is_dispatched_below_the_matching_threshold(): void
    {
        Queue::fake();

        [$user] = $this->candidate(alertsEnabled: true, attributes: [
            'desired_jobs' => 'Comptable',
            'city' => 'Paris',
            'region' => 'Île-de-France',
            'mobility' => 'local',
            'experience_level' => 'junior',
            'contract_type' => 'CDD',
        ]);

        $offer = $this->matchingOffer();

        app(MatchingService::class)->computeForOffer($offer);

        Queue::assertNotPushed(SendSmsJob::class);
        $this->assertDatabaseHas('matches', [
            'user_id' => $user->id,
            'offer_id' => $offer->id,
        ]);
    }

    /**
     * @return array{0: User, 1: CandidateProfile}
     */
    private function candidate(bool $alertsEnabled, array $attributes = []): array
    {
        $user = User::factory()->create([
            'phone' => '+261340000000',
        ]);

        $profile = CandidateProfile::factory()->create(array_merge([
            'user_id' => $user->id,
            'alerts_enabled' => $alertsEnabled,
            'desired_jobs' => 'Développeur Laravel',
            'city' => 'Antananarivo',
            'region' => 'Analamanga',
            'mobility' => 'remote',
            'experience_level' => 'mid',
            'contract_type' => 'CDI',
        ], $attributes));

        return [$user, $profile];
    }

    private function matchingOffer(): Offer
    {
        return Offer::factory()->create([
            'title' => 'Développeur Laravel',
            'company' => 'TechCorp',
            'location' => 'Antananarivo',
            'contract_type' => 'CDI',
            'status' => 'active',
        ]);
    }
}
