<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_users_list(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    public function test_users_list_displays_active_subscription_sms_values(): void
    {
        $plan = Plan::create([
            'name' => 'premium',
            'price' => 10,
            'duration_days' => 30,
            'sms_quota' => 5,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
        $user = User::factory()->create();
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'sms_remaining' => 3,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertOk();
        $response->assertSee('3 / 5');
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Nouvel Utilisateur',
            'email' => 'nouveau@example.com',
            'phone' => '+261341234567',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => 'candidate',
            'status' => 'active',
            'plan' => 'free',
            'sms_quota' => 2,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'nouveau@example.com',
            'role' => 'candidate',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name' => 'Nom Modifié',
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => 'recruiter',
            'status' => 'active',
            'plan' => 'free',
            'sms_quota' => 5,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nom Modifié',
            'role' => 'recruiter',
        ]);
    }

    public function test_update_validation_errors_are_visible_in_the_edit_modal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->from('/admin/users')
            ->put("/admin/users/{$user->id}", [
                '_user_id' => $user->id,
                'name' => $user->name,
                'email' => 'invalid-email',
                'phone' => $user->phone,
                'role' => $user->role,
                'status' => $user->status,
                'plan' => $user->plan,
                'sms_quota' => $user->sms_quota,
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertRedirect('/admin/users')->assertSessionHasErrors('email');

        $this->get('/admin/users')
            ->assertSee('alert-danger')
            ->assertSee('data-auto-open="true"')
            ->assertSee('The email field must be a valid email address.');
    }

    public function test_admin_can_update_user_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
            'role' => $user->role,
            'status' => $user->status,
            'plan' => $user->plan,
            'sms_quota' => $user->sms_quota,
        ]);

        $response->assertRedirect('/admin/users');
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_non_admin_cannot_access_user_crud(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/users')->assertStatus(403);
        $this->actingAs($candidate)->post('/admin/users')->assertStatus(403);
    }

    public function test_guest_cannot_access_user_crud(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->post('/admin/users')->assertRedirect('/login');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        User::factory()->admin()->create();
        User::factory()->candidate()->create();

        $response = $this->actingAs($this->admin)->get('/admin/users?role=admin');

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_search_users(): void
    {
        User::factory()->create(['name' => 'Jean Dupont']);
        User::factory()->create(['name' => 'Marie Martin']);

        $response = $this->actingAs($this->admin)->get('/admin/users?search=Jean');

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }
}
