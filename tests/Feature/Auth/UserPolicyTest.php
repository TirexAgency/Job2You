<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('view', $user));
    }

    public function test_user_cannot_view_other_profile(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse($user->can('view', $otherUser));
    }

    public function test_admin_can_view_any_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->assertTrue($admin->can('view', $user));
    }

    public function test_user_can_update_own_profile(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('update', $user));
    }

    public function test_user_cannot_update_other_profile(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse($user->can('update', $otherUser));
    }

    public function test_admin_can_update_any_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->assertTrue($admin->can('update', $user));
    }

    public function test_user_can_delete_own_profile(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('delete', $user));
    }

    public function test_admin_cannot_delete_own_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_can_delete_other_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->assertTrue($admin->can('delete', $user));
    }

    public function test_admin_can_view_any_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
    }

    public function test_user_cannot_view_any_user(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', User::class));
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('manage', User::class));
    }

    public function test_user_cannot_manage_users(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('manage', User::class));
    }
}
