<?php

namespace Tests\Feature\Admin;

use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_matching_settings(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');

        $response->assertOk();
        $response->assertViewIs('admin.settings.edit');
        $response->assertSee('Poids - Compétences');
        $response->assertSee('Seuil d’alerte SMS');
        $response->assertSee('0.40');
    }

    public function test_admin_can_update_matching_settings(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'weights' => [
                'skills' => 0.50,
                'job' => 0.20,
                'location' => 0.10,
                'experience' => 0.10,
                'contract' => 0.10,
            ],
            'alert_threshold' => 0.80,
        ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success', 'Paramètres de matching mis à jour avec succès.');

        $this->assertDatabaseHas('settings', ['key' => 'matching.skills_weight', 'value' => '0.5']);
        $this->assertDatabaseHas('settings', ['key' => 'matching.alert_threshold', 'value' => '0.8']);
        $this->assertSame(0.5, Setting::get('matching.skills_weight'));
        $this->assertSame(0.8, Setting::get('matching.alert_threshold'));
    }

    public function test_weights_must_sum_to_one(): void
    {
        $response = $this->actingAs($this->admin)
            ->from('/admin/settings')
            ->put('/admin/settings', [
                'weights' => [
                    'skills' => 0.40,
                    'job' => 0.25,
                    'location' => 0.15,
                    'experience' => 0.10,
                    'contract' => 0.05,
                ],
                'alert_threshold' => 0.70,
            ]);

        $response->assertRedirect('/admin/settings');
        $response->assertSessionHasErrors('weights');
        $this->assertDatabaseHas('settings', ['key' => 'matching.skills_weight', 'value' => '0.40']);
    }

    public function test_invalid_individual_weight_and_threshold_are_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->from('/admin/settings')
            ->put('/admin/settings', [
                'weights' => [
                    'skills' => 1.20,
                    'job' => 0.25,
                    'location' => 0.15,
                    'experience' => 0.10,
                    'contract' => 0.10,
                ],
                'alert_threshold' => 1.50,
            ]);

        $response->assertSessionHasErrors(['weights.skills', 'alert_threshold']);
    }

    public function test_dashboard_uses_the_threshold_saved_in_settings(): void
    {
        Setting::set('matching.alert_threshold', 0.90, Setting::TYPE_DECIMAL);

        $candidate = User::factory()->candidate()->create();
        $offers = Offer::factory()->count(2)->create();

        JobMatch::create(['user_id' => $candidate->id, 'offer_id' => $offers[0]->id, 'score' => 0.80]);
        JobMatch::create(['user_id' => $candidate->id, 'offer_id' => $offers[1]->id, 'score' => 0.95]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewHas('stats', fn (array $stats): bool => $stats['matches']['above_threshold'] === 1);
    }

    public function test_non_admin_cannot_access_matching_settings(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/settings')->assertForbidden();
        $this->actingAs($candidate)->put('/admin/settings', [
            'weights' => [
                'skills' => 0.40,
                'job' => 0.25,
                'location' => 0.15,
                'experience' => 0.10,
                'contract' => 0.10,
            ],
            'alert_threshold' => 0.70,
        ])->assertForbidden();
    }

    public function test_guest_cannot_access_matching_settings(): void
    {
        $this->get('/admin/settings')->assertRedirect('/login');
    }
}
