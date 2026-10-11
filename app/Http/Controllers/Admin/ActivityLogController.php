<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    /**
     * Affiche les logs d'activité métier.
     */
    public function __invoke(Request $request)
    {
        $logFile = storage_path('logs/activity.log');
        $lines = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $lines = array_reverse(array_filter(explode("\n", $content)));
        }

        // Filtrage par niveau si demandé
        $level = $request->get('level');
        if ($level && in_array($level, ['info', 'warning', 'error'])) {
            $lines = array_filter($lines, fn ($line) => str_contains($line, "\"level\":\"{$level}\"") || str_contains($line, "\"level\":\"{$level}\""));
        }

        // Recherche textuelle
        $search = $request->get('search');
        if ($search) {
            $lines = array_filter($lines, fn ($line) => stripos($line, $search) !== false);
        }

        // Limiter aux 500 dernières lignes
        $lines = array_slice($lines, 0, 500);

        // Parser les lignes JSON pour affichage structuré
        $parsedLines = [];
        foreach ($lines as $line) {
            $parsed = $this->parseLogLine($line);
            if ($parsed) {
                $parsedLines[] = $parsed;
            }
        }

        return view('admin.activity-log.index', compact('parsedLines', 'level', 'search'));
    }

    /**
     * Parse une ligne de log JSON.
     */
    private function parseLogLine(string $line): ?array
    {
        $line = trim($line);
        if (empty($line)) {
            return null;
        }

        // Essayer de décoder le JSON
        $data = json_decode($line, true);
        if (! $data) {
            return ['raw' => $line, 'time' => '', 'level' => 'info', 'message' => $line, 'context' => []];
        }

        return [
            'time' => $data['datetime'] ?? '',
            'level' => $data['level_name'] ?? 'info',
            'message' => $data['message'] ?? '',
            'context' => $data['context'] ?? [],
        ];
    }
}
