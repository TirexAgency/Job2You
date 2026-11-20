<?php

namespace Tests\Feature\Admin;

use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SourceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_configure_a_source(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/sources', [
            'name' => 'Nouvelle source',
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss_new_source',
            'active' => 1,
        ]);

        $response->assertRedirect('/admin/sources');
        $this->assertDatabaseHas('sources', [
            'name' => 'Nouvelle source',
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss_new_source',
            'active' => true,
        ]);
    }

    public function test_admin_can_toggle_and_update_a_source(): void
    {
        $source = Source::factory()->create([
            'name' => 'Source à configurer',
            'collector_key' => 'rss_config_test',
            'active' => true,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.sources.toggle', $source))
            ->assertRedirect();

        $this->assertFalse($source->fresh()->active);

        $this->actingAs($this->admin)
            ->put(route('admin.sources.update', $source), [
                'name' => 'Source configurée',
                'base_url' => 'https://example.com/new-feed.rss',
                'collector_key' => 'rss_config_test',
                'active' => 1,
            ])
            ->assertRedirect('/admin/sources');

        $this->assertSame('Source configurée', $source->fresh()->name);
        $this->assertSame('https://example.com/new-feed.rss', $source->fresh()->base_url);
    }

    public function test_admin_can_delete_a_source(): void
    {
        $source = Source::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.sources.destroy', $source))
            ->assertRedirect('/admin/sources');

        $this->assertDatabaseMissing('sources', ['id' => $source->id]);
    }

    public function test_non_admin_cannot_manage_sources(): void
    {
        $candidate = User::factory()->candidate()->create();
        $source = Source::factory()->create();

        $this->actingAs($candidate)->get('/admin/sources')->assertForbidden();
        $this->actingAs($candidate)->patch(route('admin.sources.toggle', $source))->assertForbidden();
        $this->actingAs($candidate)->delete(route('admin.sources.destroy', $source))->assertForbidden();
    }
}
