<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_activity_log_filters_by_level_and_search(): void
    {
        $logFile = $this->writeLogFile([
            ['datetime' => '2026-11-20 10:00:00', 'level_name' => 'INFO', 'message' => 'Info unique-alpha', 'context' => []],
            ['datetime' => '2026-11-20 10:05:00', 'level_name' => 'ERROR', 'message' => 'Erreur unique-beta', 'context' => ['source' => 'test']],
        ]);

        try {
            $response = $this->actingAs($this->admin)
                ->get('/admin/activity-log?level=error&search=unique-beta');

            $response->assertOk();
            $response->assertViewIs('admin.activity-log.index');
            $response->assertSee('Erreur unique-beta');
            $response->assertDontSee('Info unique-alpha');
            $response->assertViewHas('levelCounts', fn (array $counts): bool => $counts['error'] === 1 && $counts['info'] === 1);
        } finally {
            File::delete($logFile);
        }
    }

    public function test_raw_logs_apply_filters(): void
    {
        $logFile = $this->writeLogFile([
            ['datetime' => '2026-11-20 10:00:00', 'level_name' => 'INFO', 'message' => 'Brut unique-gamma', 'context' => []],
            ['datetime' => '2026-11-20 10:05:00', 'level_name' => 'ERROR', 'message' => 'Brut unique-delta', 'context' => []],
        ]);

        try {
            $response = $this->actingAs($this->admin)
                ->get('/admin/logs?level=error&search=unique-delta&lines=100');

            $response->assertOk();
            $response->assertViewIs('admin.logs.index');
            $response->assertSee('Brut unique-delta');
            $response->assertDontSee('Brut unique-gamma');
            $response->assertViewHas('level', 'error');
            $response->assertViewHas('linesCount', 100);
        } finally {
            File::delete($logFile);
        }
    }

    public function test_non_admin_cannot_access_logs(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/activity-log')->assertForbidden();
        $this->actingAs($candidate)->get('/admin/logs')->assertForbidden();
    }

    /**
     * @param  array<int, array<string, mixed>>  $entries
     */
    private function writeLogFile(array $entries): string
    {
        $path = storage_path('logs/activity-2999-01-01.log');
        File::ensureDirectoryExists(dirname($path));

        $lines = array_map(
            fn (array $entry): string => json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $entries,
        );

        File::put($path, implode("\n", $lines));

        return $path;
    }
}
