<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlanFeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_is_blocked_from_cv_parsing(): void
    {
        Storage::fake('local');

        $freePlan = Plan::factory()->free()->create();
        $user = User::factory()->create();
        $this->activeSubscription($user, $freePlan);

        $response = $this->actingAs($user)->post(route('candidate.cv.upload'), [
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('cv_parses', 0);
    }

    public function test_premium_plan_grants_cv_parsing_feature(): void
    {
        Storage::fake('local');

        $premiumPlan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
        $user = User::factory()->create();
        $this->activeSubscription($user, $premiumPlan);

        $response = $this->actingAs($user)->post(route('candidate.cv.upload'), [
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('candidate.cv'));
        $this->assertDatabaseHas('cv_parses', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_expired_premium_subscription_is_blocked_from_cv_parsing(): void
    {
        Notification::fake();
        Storage::fake('local');

        Plan::factory()->free()->create();
        $premiumPlan = Plan::factory()->create([
            'name' => 'premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
        $user = User::factory()->create(['plan' => 'premium', 'sms_quota' => 50]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $premiumPlan->id,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
            'sms_remaining' => 50,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('candidate.cv.upload'), [
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('cv_parses', 0);
    }

    public function test_plan_features_are_derived_from_quotas_and_options(): void
    {
        $freePlan = Plan::factory()->free()->create(['sms_quota' => 2]);
        $premiumPlan = Plan::factory()->create([
            'price' => 85000,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
        ]);

        $this->assertTrue($freePlan->hasFeature(Plan::FEATURE_SMS_ALERTS));
        $this->assertFalse($freePlan->hasFeature(Plan::FEATURE_CV_PARSING));
        $this->assertTrue($premiumPlan->hasFeature(Plan::FEATURE_SMS_ALERTS));
        $this->assertTrue($premiumPlan->hasFeature(Plan::FEATURE_CV_PARSING));
        $this->assertContains('Analyse de CV par IA', $premiumPlan->featureList());
    }

    private function activeSubscription(User $user, Plan $plan): Subscription
    {
        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => $plan->sms_quota,
            'status' => 'active',
        ]);
    }
}
