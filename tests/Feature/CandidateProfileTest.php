<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Skill;
use App\Models\SmsLog;
use App\Models\Source;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CandidateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_is_saved_with_location_and_mobility(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('candidate.profile.update'), [
            'desired_jobs' => 'Développeur PHP',
            'experience_level' => 'mid',
            'city' => 'Antananarivo',
            'region' => 'Analamanga',
            'mobility' => 'national',
            'contract_type' => 'cdi',
            'desired_salary' => 2500000,
            'sectors_text' => "Tech\nFinance",
            'alerts_enabled' => '1',
        ]);

        $response->assertRedirect(route('candidate.profile.edit'));
        $profile = $user->fresh()->candidateProfile;
        $this->assertNotNull($profile);
        $this->assertSame('Antananarivo', $profile->city);
        $this->assertSame('national', $profile->mobility);
        $this->assertSame('cdi', $profile->contract_type);
        $this->assertSame(2500000, $profile->desired_salary);
        $this->assertSame(['Tech', 'Finance'], $profile->sectors);
        $this->assertTrue($profile->alerts_enabled);
    }

    public function test_profile_requires_city_and_valid_mobility(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('candidate.profile.update'), [
            'experience_level' => 'mid',
            'city' => '',
            'mobility' => 'partout',
        ]);

        $response->assertSessionHasErrors(['city', 'mobility']);
    }

    public function test_experience_is_added_and_sorted_chronologically(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.experiences.store'), [
            'company' => 'Ancienne Corp',
            'position' => 'Dev',
            'start_date' => '2019-01-01',
            'end_date' => '2021-06-01',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('candidate.experiences.store'), [
            'company' => 'Nouvelle Corp',
            'position' => 'Lead Dev',
            'start_date' => '2022-03-01',
            'end_date' => null,
        ])->assertRedirect();

        $experiences = $user->fresh()->candidateProfile->experiences;
        $this->assertCount(2, $experiences);
        $this->assertSame('Nouvelle Corp', $experiences->first()->company);
        $this->assertSame('Ancienne Corp', $experiences->last()->company);
    }

    public function test_experience_update_and_delete_require_ownership(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $profile = CandidateProfile::create(['user_id' => $user->id, 'experience_level' => 'junior']);
        $experience = Experience::create([
            'candidate_profile_id' => $profile->id,
            'company' => 'ACME',
            'position' => 'Dev',
            'start_date' => '2020-01-01',
        ]);

        $this->actingAs($other)->delete(route('candidate.experiences.destroy', $experience))
            ->assertForbidden();

        $this->actingAs($user)->delete(route('candidate.experiences.destroy', $experience))
            ->assertRedirect();
        $this->assertDatabaseMissing('experiences', ['id' => $experience->id]);
    }

    public function test_experience_end_date_must_be_after_start_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.experiences.store'), [
            'company' => 'ACME',
            'position' => 'Dev',
            'start_date' => '2023-01-01',
            'end_date' => '2022-01-01',
        ])->assertSessionHasErrors('end_date');
    }

    public function test_skill_is_added_with_level_and_removed(): void
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Laravel', 'normalized_name' => 'laravel']);

        $this->actingAs($user)->post(route('candidate.skills.store'), [
            'skill_id' => $skill->id,
            'level' => 4,
        ])->assertRedirect();

        $profile = $user->fresh()->candidateProfile;
        $this->assertSame(4, $profile->candidateSkills->first()->level);

        $this->actingAs($user)->delete(route('candidate.skills.destroy', ['candidateSkill' => $skill->id]))
            ->assertRedirect();
        $this->assertCount(0, $profile->fresh()->candidateSkills);
    }

    public function test_skill_can_be_created_by_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.skills.store'), [
            'name' => 'Vue.js',
            'level' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('skills', ['normalized_name' => 'vue.js']);
        $this->assertSame(3, $user->fresh()->candidateProfile->candidateSkills->first()->level);
    }

    public function test_education_is_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.educations.store'), [
            'school' => 'Université d\'Antananarivo',
            'degree' => 'Licence Informatique',
            'field' => 'Informatique',
            'start_date' => '2018-09-01',
            'end_date' => '2021-06-30',
        ])->assertRedirect();

        $education = $user->fresh()->candidateProfile->educations->first();
        $this->assertInstanceOf(Education::class, $education);
        $this->assertSame('Licence Informatique', $education->degree);
    }

    public function test_candidate_can_change_plan(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'sms_quota' => 2]);
        $plan = Plan::create(['name' => 'Premium', 'price' => 49000, 'duration_days' => 30, 'sms_quota' => 50, 'active' => true]);

        $this->actingAs($user)->post(route('candidate.subscription.change'), ['plan_id' => $plan->id])
            ->assertRedirect(route('candidate.subscription'));

        $this->assertSame('premium', $user->fresh()->plan);
        $this->assertSame(50, $user->fresh()->sms_quota);
        $this->assertDatabaseHas('subscriptions', ['user_id' => $user->id, 'plan_id' => $plan->id, 'status' => 'active', 'sms_remaining' => 50]);
    }

    public function test_candidate_can_toggle_sms_alerts(): void
    {
        $user = User::factory()->create();
        CandidateProfile::create(['user_id' => $user->id, 'experience_level' => 'junior', 'alerts_enabled' => false]);

        $this->actingAs($user)->post(route('candidate.sms.alerts'), ['alerts_enabled' => 1])
            ->assertRedirect(route('candidate.sms'));
        $this->assertTrue($user->fresh()->candidateProfile->alerts_enabled);

        $this->actingAs($user)->post(route('candidate.sms.alerts'), ['alerts_enabled' => 0]);
        $this->assertFalse($user->fresh()->candidateProfile->alerts_enabled);
    }

    public function test_candidate_can_delete_sms_log(): void
    {
        $user = User::factory()->create();
        $source = Source::create(['name' => 'Test', 'collector_key' => 'test']);
        $offer = Offer::create(['source_id' => $source->id, 'external_id' => '1', 'title' => 'Dev']);
        $plan = Plan::create(['name' => 'Free', 'price' => 0, 'duration_days' => 30, 'sms_quota' => 2, 'active' => true]);
        $subscription = Subscription::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'status' => 'active', 'sms_remaining' => 2]);
        $log = SmsLog::create(['user_id' => $user->id, 'offer_id' => $offer->id, 'subscription_id' => $subscription->id, 'status' => 'sent', 'sent_at' => now()]);

        $this->actingAs($user)->delete(route('candidate.sms.destroy', $log))
            ->assertRedirect(route('candidate.sms'));
        $this->assertDatabaseMissing('sms_logs', ['id' => $log->id]);
    }

    public function test_free_plan_cannot_upload_cv(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('candidate.cv.upload'), [
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertForbidden();
    }

    public function test_premium_plan_can_upload_cv(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create(['name' => 'premium', 'price' => 15000, 'duration_days' => 30, 'sms_quota' => 50, 'cv_parsing_enabled' => true, 'active' => true]);
        Subscription::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'status' => 'active', 'sms_remaining' => 50]);

        $this->actingAs($user)->post(route('candidate.cv.upload'), [
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('candidate.cv'));

        $this->assertDatabaseHas('cv_parses', ['user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_admin_can_toggle_source(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $source = Source::create(['name' => 'S', 'collector_key' => 's1', 'active' => true]);

        $this->actingAs($admin)->patch(route('admin.sources.toggle', $source))->assertSessionHasNoErrors();
        $this->assertFalse($source->fresh()->active);
    }

    public function test_matching_service_scores_profile_against_offer(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::create(['user_id' => $user->id, 'desired_jobs' => 'Développeur', 'location' => 'Paris', 'contract_type' => 'cdi', 'experience_level' => 'junior']);
        $source = Source::create(['name' => 'S', 'collector_key' => 's2', 'active' => true]);
        $offer = Offer::create(['source_id' => $source->id, 'external_id' => '1', 'title' => 'Développeur PHP', 'location' => 'Paris', 'contract_type' => 'cdi', 'status' => 'active']);

        $result = app(MatchingService::class)->scoreFor($profile, $offer);

        $this->assertGreaterThan(0.5, $result['score']);
        $this->assertDatabaseMissing('matches', ['user_id' => $user->id, 'offer_id' => $offer->id]);

        app(MatchingService::class)->computeForProfile($profile);
        $this->assertDatabaseHas('matches', ['user_id' => $user->id, 'offer_id' => $offer->id]);
    }

    public function test_skill_level_can_be_updated(): void
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Laravel', 'normalized_name' => 'laravel']);
        $this->actingAs($user)->post(route('candidate.skills.store'), ['skill_id' => $skill->id, 'level' => 2]);

        $this->actingAs($user)->put(route('candidate.skills.update', ['candidateSkill' => $skill->id]), ['level' => 5])
            ->assertRedirect(route('candidate.profile.edit'));

        $this->assertSame(5, $user->fresh()->candidateProfile->candidateSkills->first()->level);
    }

    public function test_experience_can_be_updated(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::create(['user_id' => $user->id, 'experience_level' => 'junior']);
        $experience = Experience::create(['candidate_profile_id' => $profile->id, 'company' => 'ACME', 'position' => 'Dev', 'start_date' => '2020-01-01']);

        $this->actingAs($user)->put(route('candidate.experiences.update', $experience), [
            'company' => 'ACME Corp', 'position' => 'Lead', 'start_date' => '2020-01-01', 'end_date' => null,
        ])->assertRedirect(route('candidate.profile.edit'));

        $this->assertSame('ACME Corp', $experience->fresh()->company);
    }

    public function test_education_can_be_updated(): void
    {
        $user = User::factory()->create();
        $profile = CandidateProfile::create(['user_id' => $user->id, 'experience_level' => 'junior']);
        $education = Education::create(['candidate_profile_id' => $profile->id, 'school' => 'Univ A', 'degree' => 'Licence', 'start_date' => '2018-09-01']);

        $this->actingAs($user)->put(route('candidate.educations.update', $education), [
            'school' => 'Univ B', 'degree' => 'Master', 'start_date' => '2020-09-01', 'end_date' => null,
        ])->assertRedirect(route('candidate.profile.edit'));

        $this->assertSame('Master', $education->fresh()->degree);
    }

    public function test_guest_cannot_access_profile_page(): void
    {
        $this->get(route('candidate.profile.edit'))->assertRedirect(route('login'));
    }
}
