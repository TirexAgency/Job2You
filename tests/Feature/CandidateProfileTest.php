<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_guest_cannot_access_profile_page(): void
    {
        $this->get(route('candidate.profile.edit'))->assertRedirect(route('login'));
    }
}
