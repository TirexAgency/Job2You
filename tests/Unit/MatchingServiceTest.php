<?php

namespace Tests\Unit;

use App\Models\CandidateProfile;
use App\Models\Offer;
use App\Models\OfferSkill;
use App\Models\Skill;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MatchingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MatchingService::class);

        // Configurer les settings par défaut pour les tests
        \App\Models\Setting::set('matching.skills_weight', '0.40', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.job_weight', '0.25', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.location_weight', '0.15', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.experience_weight', '0.10', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.contract_weight', '0.10', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.alert_threshold', '0.70', \App\Models\Setting::TYPE_DECIMAL);
    }

    #[Test]
    public function score_for_returns_structured_result_with_score_and_details(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'desired_jobs' => 'Developpeur Laravel',
            'city' => 'Paris',
            'mobility' => 'local',
            'experience_level' => 'senior',
            'contract_type' => 'CDI',
        ]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur Laravel Senior',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertArrayHasKey('meets_threshold', $result);
        $this->assertArrayHasKey('threshold', $result);
        $this->assertIsFloat($result['score']);
        $this->assertGreaterThanOrEqual(0.0, $result['score']);
        $this->assertLessThanOrEqual(1.0, $result['score']);
        $this->assertIsArray($result['details']);
    }

    #[Test]
    public function perfect_match_returns_high_score(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'desired_jobs' => 'Developpeur Laravel',
            'city' => 'Paris',
            'mobility' => 'local',
            'experience_level' => 'senior',
            'contract_type' => 'CDI',
        ]);

        $skillPhp = Skill::factory()->create(['name' => 'PHP']);
        $skillLaravel = Skill::factory()->create(['name' => 'Laravel']);
        $profile->skills()->attach([$skillPhp->id, $skillLaravel->id]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur Laravel Senior',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ]);

        $offer->skills()->attach([$skillPhp->id, $skillLaravel->id]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(1.0, $result['details']['skills']);
        $this->assertEquals(1.0, $result['details']['contract']);
        $this->assertEquals(1.0, $result['details']['location']);
        $this->assertGreaterThan(0.85, $result['score']);
    }

    #[Test]
    public function no_skills_match_returns_low_skills_score(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'desired_jobs' => 'Developpeur Laravel',
            'city' => 'Paris',
            'mobility' => 'local',
            'experience_level' => 'senior',
            'contract_type' => 'CDI',
        ]);

        $skillPhp = Skill::factory()->create(['name' => 'PHP']);
        $profile->skills()->attach([$skillPhp->id]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur Laravel Senior',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ]);

        $skillLaravel = Skill::factory()->create(['name' => 'Laravel']);
        $offer->skills()->attach([$skillLaravel->id]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(0.0, $result['details']['skills']);
    }

    #[Test]
    public function offer_without_skills_returns_neutral_skills_score(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur',
            'company' => 'TechCorp',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(0.5, $result['details']['skills']);
    }

    #[Test]
    public function location_score_remote_mobility_returns_max(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'city' => 'Paris',
            'mobility' => 'remote',
        ]);

        $offer = Offer::factory()->create([
            'location' => 'Remote',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(1.0, $result['details']['location']);
    }

    #[Test]
    public function location_score_same_city_returns_max(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'city' => 'Paris',
            'mobility' => 'local',
        ]);

        $offer = Offer::factory()->create([
            'location' => 'Paris 15eme',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(1.0, $result['details']['location']);
    }

    #[Test]
    public function location_score_different_city_returns_low(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'city' => 'Paris',
            'mobility' => 'local',
        ]);

        $offer = Offer::factory()->create([
            'location' => 'Lyon',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(0.3, $result['details']['location']);
    }

    #[Test]
    public function contract_score_exact_match_returns_max(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'contract_type' => 'CDI',
        ]);

        $offer = Offer::factory()->create([
            'contract_type' => 'CDI',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(1.0, $result['details']['contract']);
    }

    #[Test]
    public function contract_score_no_preference_returns_high(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'contract_type' => 'any',
        ]);

        $offer = Offer::factory()->create([
            'contract_type' => 'CDI',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(0.8, $result['details']['contract']);
    }

    #[Test]
    public function contract_score_mismatch_returns_zero(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'contract_type' => 'CDI',
        ]);

        $offer = Offer::factory()->create([
            'contract_type' => 'Freelance',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(0.0, $result['details']['contract']);
    }

    #[Test]
    public function experience_score_same_level_returns_max(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'experience_level' => 'senior',
        ]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur Senior Laravel',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertEquals(1.0, $result['details']['experience']);
    }

    #[Test]
    public function experience_score_junior_vs_senior_returns_low(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'experience_level' => 'junior',
        ]);

        $offer = Offer::factory()->create([
            'title' => 'Lead Developer / Architect',
        ]);

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertLessThanOrEqual(0.55, $result['details']['experience']);
    }

    #[Test]
    public function score_is_bounded_between_0_and_1(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $offer = Offer::factory()->create();

        $result = $this->service->scoreFor($profile, $offer);

        $this->assertGreaterThanOrEqual(0.0, $result['score']);
        $this->assertLessThanOrEqual(1.0, $result['score']);
    }

    #[Test]
    public function meets_threshold_uses_setting_value(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
            'desired_jobs' => 'Developpeur Laravel',
            'city' => 'Paris',
            'mobility' => 'local',
            'experience_level' => 'senior',
            'contract_type' => 'CDI',
        ]);

        $skillPhp = Skill::factory()->create(['name' => 'PHP']);
        $skillLaravel = Skill::factory()->create(['name' => 'Laravel']);
        $profile->skills()->attach([$skillPhp->id, $skillLaravel->id]);

        $offer = Offer::factory()->create([
            'title' => 'Developpeur Laravel Senior',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ]);
        $offer->skills()->attach([$skillPhp->id, $skillLaravel->id]);

        // Seuil par defaut 0.70
        $result = $this->service->scoreFor($profile, $offer);
        $this->assertEquals(0.70, $result['threshold']);
        $this->assertTrue($result['meets_threshold']);

        // Test avec un profil moins parfait pour avoir un score bas (nouvel utilisateur)
        $user2 = User::factory()->create();
        $profile2 = CandidateProfile::factory()->create([
            'user_id' => $user2->id,
            'desired_jobs' => 'Developpeur Symfony',
            'city' => 'Lyon',
            'mobility' => 'local',
            'experience_level' => 'junior',
            'contract_type' => 'CDD',
        ]);

        $offer2 = Offer::factory()->create([
            'title' => 'Developpeur Laravel Senior',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ]);
        $offer2->skills()->attach([$skillPhp->id, $skillLaravel->id]);

        $result3 = $this->service->scoreFor($profile2, $offer2);
        $lowScore = $result3['score'];

        // Definir un seuil juste au-dessus de ce score bas
        $newThreshold = min($lowScore + 0.15, 0.95);
        \App\Models\Setting::set('matching.alert_threshold', (string)$newThreshold, \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::forget('matching.alert_threshold');

        $result4 = $this->service->scoreFor($profile2, $offer2);
        $this->assertEquals($newThreshold, $result4['threshold']);
        $this->assertFalse($result4['meets_threshold']);
    }

    #[Test]
    public function weights_are_loaded_from_settings(): void
    {
        \App\Models\Setting::set('matching.skills_weight', '0.50', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.job_weight', '0.20', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.location_weight', '0.10', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.experience_weight', '0.10', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::set('matching.contract_weight', '0.10', \App\Models\Setting::TYPE_DECIMAL);
        \App\Models\Setting::forget('matching.skills_weight');
        \App\Models\Setting::forget('matching.job_weight');
        \App\Models\Setting::forget('matching.location_weight');
        \App\Models\Setting::forget('matching.experience_weight');
        \App\Models\Setting::forget('matching.contract_weight');

        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $offer = Offer::factory()->create();

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getWeights');
        $method->setAccessible(true);
        $weights = $method->invoke($this->service);

        $this->assertEquals(0.50, $weights['skills']);
        $this->assertEquals(0.20, $weights['job']);
        $this->assertEquals(0.10, $weights['location']);
        $this->assertEquals(0.10, $weights['experience']);
        $this->assertEquals(0.10, $weights['contract']);
    }

    #[Test]
    public function compute_for_profile_creates_job_matches(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        Offer::factory()->count(3)->active()->create();

        $count = $this->service->computeForProfile($profile);

        $this->assertEquals(3, $count);
        $this->assertDatabaseCount('matches', 3);
        $this->assertDatabaseHas('matches', ['user_id' => $user->id]);
    }

    #[Test]
    public function compute_for_offer_creates_matches_for_all_profiles(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        CandidateProfile::factory()->create(['user_id' => $user1->id]);
        CandidateProfile::factory()->create(['user_id' => $user2->id]);

        $offer = Offer::factory()->active()->create();

        $count = $this->service->computeForOffer($offer);

        $this->assertEquals(2, $count);
        $this->assertDatabaseCount('matches', 2);
        $this->assertDatabaseHas('matches', ['offer_id' => $offer->id, 'user_id' => $user1->id]);
        $this->assertDatabaseHas('matches', ['offer_id' => $offer->id, 'user_id' => $user2->id]);
    }

    #[Test]
    public function get_recommendations_returns_only_above_threshold(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::factory()->create(['user_id' => $user->id]);

        $offer1 = Offer::factory()->active()->create(['title' => 'Developpeur Laravel Senior']);
        $offer2 = Offer::factory()->active()->create(['title' => 'Comptable Junior']);

        \App\Models\JobMatch::create([
            'user_id' => $user->id,
            'offer_id' => $offer1->id,
            'score' => 0.85,
            'details_json' => ['skills' => 1, 'job' => 1, 'location' => 1, 'experience' => 1, 'contract' => 1],
        ]);
        \App\Models\JobMatch::create([
            'user_id' => $user->id,
            'offer_id' => $offer2->id,
            'score' => 0.40,
            'details_json' => ['skills' => 0, 'job' => 0, 'location' => 0, 'experience' => 0, 'contract' => 0],
        ]);

        $recommendations = $this->service->getRecommendations($user->id, 10);

        $this->assertCount(1, $recommendations);
        $this->assertEquals($offer1->id, $recommendations->first()->offer_id);
    }
}