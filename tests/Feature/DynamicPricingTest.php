<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_page_displays_active_plans_from_database(): void
    {
        Plan::factory()->free()->create(['sms_quota' => 2]);
        Plan::factory()->create([
            'name' => 'starter',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);

        $response = $this->get(route('pricing'));

        $response->assertOk();
        $response->assertViewIs('pricing');
        $response->assertSee('Free');
        $response->assertSee('Starter');
        $response->assertSee('85 000');
        $response->assertSee('50 alertes SMS par période');
        $response->assertSee('Analyse de CV par IA');
        $response->assertSee('Le plus choisi');
    }

    public function test_inactive_plans_are_hidden_from_pricing_page(): void
    {
        Plan::factory()->free()->create();
        Plan::factory()->inactive()->create(['name' => 'legacy']);

        $response = $this->get(route('pricing'));

        $response->assertOk();
        $response->assertDontSee('Legacy');
    }

    public function test_pricing_page_handles_empty_plan_catalog(): void
    {
        $response = $this->get(route('pricing'));

        $response->assertOk();
        $response->assertSee('Aucune formule n’est disponible', false);
    }

    public function test_authenticated_candidate_is_sent_to_subscription_page_from_catalog(): void
    {
        Plan::factory()->free()->create();
        $candidate = User::factory()->candidate()->create();

        $response = $this->actingAs($candidate)->get(route('pricing'));

        $response->assertOk();
        $response->assertSee(route('candidate.subscription'), false);
    }
}
