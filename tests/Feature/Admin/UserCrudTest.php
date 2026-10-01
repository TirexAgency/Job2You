<?php

namespace Tests\Feature\Admin;

use App\Models\User;
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

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
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

    public function test_admin_can_view_user_details(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
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
        $this->actingAs($candidate)->get('/admin/users/create')->assertStatus(403);
        $this->actingAs($candidate)->post('/admin/users')->assertStatus(403);
    }

    public function test_guest_cannot_access_user_crud(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/users/create')->assertRedirect('/login');
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
