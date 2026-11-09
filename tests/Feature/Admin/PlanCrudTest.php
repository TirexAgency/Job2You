<?php

namespace Tests\Feature\Admin;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_plans_with_prices_and_quotas(): void
    {
        Plan::factory()->create([
            'name' => 'Premium',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/plans');

        $response->assertOk();
        $response->assertViewIs('admin.plans.index');
        $response->assertSee('Premium');
        $response->assertSee('85 000 Ar');
        $response->assertSee('50 SMS');
        $response->assertSee('Incluse');
    }

    public function test_admin_can_create_plan_with_price_and_quotas(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/plans', [
            'name' => 'Pro',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect('/admin/plans');
        $response->assertSessionHas('success', 'Plan Pro créé avec succès.');
        $this->assertDatabaseHas('plans', [
            'name' => 'Pro',
            'price' => 85000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
    }

    public function test_plan_creation_rejects_invalid_price_and_quotas(): void
    {
        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->from('/admin/plans')
            ->post('/admin/plans', [
                'name' => '',
                'price' => -1,
                'duration_days' => -1,
                'sms_quota' => -1,
                'cv_parsing_enabled' => 0,
                'active' => 0,
            ]);

        $response
            ->assertSee('data-auto-open="true"', false)
            ->assertSee('Le champ nom est obligatoire.')
            ->assertSee('La valeur de price doit être supérieure ou égale à 0.')
            ->assertSee('La valeur de duration days doit être supérieure ou égale à 0.')
            ->assertSee('La valeur de sms quota doit être supérieure ou égale à 0.');
    }

    public function test_plan_name_must_be_unique(): void
    {
        Plan::factory()->create(['name' => 'premium']);

        $response = $this->actingAs($this->admin)->post('/admin/plans', [
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => 1,
            'active' => 1,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('plans', 1);
    }

    public function test_admin_can_update_plan_price_and_quotas(): void
    {
        $plan = Plan::factory()->create([
            'name' => 'Premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/plans/{$plan->id}", [
            'name' => 'Premium Plus',
            'price' => 85000,
            'duration_days' => 90,
            'sms_quota' => 120,
            'cv_parsing_enabled' => 0,
            'active' => 0,
        ]);

        $response->assertRedirect('/admin/plans');
        $response->assertSessionHas('success', 'Plan Premium Plus mis à jour avec succès.');
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Premium Plus',
            'price' => 85000,
            'duration_days' => 90,
            'sms_quota' => 120,
            'cv_parsing_enabled' => false,
            'active' => false,
        ]);
    }

    public function test_admin_can_toggle_plan_status(): void
    {
        $plan = Plan::factory()->create(['active' => true]);

        $response = $this->actingAs($this->admin)->patch("/admin/plans/{$plan->id}/toggle");

        $response->assertRedirect();
        $response->assertSessionHas('success', "Statut du plan {$plan->name} mis à jour.");
        $this->assertFalse($plan->fresh()->active);
    }

    public function test_admin_can_delete_plan_without_usage(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/plans/{$plan->id}");

        $response->assertRedirect('/admin/plans');
        $response->assertSessionHas('success', "Plan {$plan->name} supprimé avec succès.");
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_admin_cannot_delete_plan_used_by_a_subscription(): void
    {
        $plan = Plan::factory()->create();
        $plan->subscriptions()->create([
            'user_id' => User::factory()->create()->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => $plan->sms_quota,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/plans/{$plan->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error', "Le plan {$plan->name} est utilisé par des abonnements ou des paiements et ne peut pas être supprimé.");
        $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    }

    public function test_non_admin_cannot_access_plan_crud(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/plans')->assertForbidden();
        $this->actingAs($candidate)->post('/admin/plans')->assertForbidden();
    }

    public function test_guest_cannot_access_plan_crud(): void
    {
        $this->get('/admin/plans')->assertRedirect('/login');
        $this->post('/admin/plans')->assertRedirect('/login');
    }
}
