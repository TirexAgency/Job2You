<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Niveaux de log supportés par les filtres.
     *
     * @var array<int, string>
     */
    private const LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    /**
     * Nombres de lignes proposés dans les filtres.
     *
     * @var array<int, int>
     */
    private const LINE_OPTIONS = [50, 100, 200, 500];

    /**
     * Affiche les logs d'activité métier (format structuré avec filtres).
     */
    public function __invoke(Request $request): View
    {
        [$level, $search, $lines] = $this->filters($request);
        $allLines = $this->readLines('activity');
        $rawLines = $this->filterLines($allLines, $level, $search);

        $parsedLines = collect($rawLines)
            ->take($lines)
            ->map(fn (string $line): ?array => $this->parseLogLine($line))
            ->filter()
            ->values()
            ->all();

        return view('admin.activity-log.index', [
            'parsedLines' => $parsedLines,
            'level' => $level,
            'search' => $search,
            'lines' => $lines,
            'lineOptions' => self::LINE_OPTIONS,
            'levelCounts' => $this->levelCounts($allLines),
            'logFile' => $this->logFilePath('activity'),
        ]);
    }

    /**
     * Affiche les logs bruts (admin/logs).
     */
    public function rawLogs(Request $request): View
    {
        [$level, $search, $lines] = $this->filters($request);
        $source = $request->string('source')->toString() === 'laravel' ? 'laravel' : 'activity';
        $allLines = $this->readLines($source);
        $rawLines = $this->filterLines($allLines, $level, $search);

        return view('admin.logs.index', [
            'logContent' => implode("\n", array_slice($rawLines, 0, $lines)),
            'linesCount' => $lines,
            'lineOptions' => self::LINE_OPTIONS,
            'level' => $level,
            'search' => $search,
            'source' => $source,
            'levelCounts' => $this->levelCounts($allLines),
            'logFile' => $this->logFilePath($source),
        ]);
    }

    /**
     * Extrait et normalise les filtres de la requête.
     *
     * @return array{0: ?string, 1: ?string, 2: int}
     */
    private function filters(Request $request): array
    {
        $level = $request->string('level')->toString();
        $level = in_array($level, self::LEVELS, true) ? $level : null;

        $search = trim($request->string('search')->toString());
        $search = $search !== '' ? $search : null;

        $lines = (int) $request->integer('lines', 100);
        $lines = in_array($lines, self::LINE_OPTIONS, true) ? $lines : 100;

        return [$level, $search, $lines];
    }

    /**
     * Lit et inverse les lignes du journal demandé.
     *
     * @return array<int, string>
     */
    private function readLines(string $source): array
    {
        $logFile = $this->logFilePath($source);

        if (! $logFile || ! File::exists($logFile)) {
            return [];
        }

        return array_values(array_reverse(array_filter(explode("\n", File::get($logFile)))));
    }

    /**
     * Applique les filtres de niveau et de recherche.
     *
     * @param  array<int, string>  $lines
     * @return array<int, string>
     */
    private function filterLines(array $lines, ?string $level, ?string $search): array
    {
        return array_values(array_filter($lines, function (string $line) use ($level, $search): bool {
            if ($level !== null && $this->lineLevel($line) !== $level) {
                return false;
            }

            return $search === null || stripos($line, $search) !== false;
        }));
    }

    /**
     * Compte les lignes par niveau.
     *
     * @param  array<int, string>  $lines
     * @return array<string, int>
     */
    private function levelCounts(array $lines): array
    {
        $counts = array_fill_keys(self::LEVELS, 0);

        foreach ($lines as $line) {
            $level = $this->lineLevel($line);

            if ($level !== null) {
                $counts[$level]++;
            }
        }

        return $counts;
    }

    /**
     * Détermine le niveau d'une ligne de log JSON.
     */
    private function lineLevel(string $line): ?string
    {
        $data = json_decode(trim($line), true);

        if (is_array($data)) {
            $level = strtolower((string) ($data['level_name'] ?? $data['level'] ?? ''));

            return in_array($level, self::LEVELS, true) ? $level : null;
        }

        if (preg_match('/\.(debug|info|notice|warning|error|critical|alert|emergency):/i', $line, $matches) === 1) {
            return strtolower($matches[1]);
        }

        return null;
    }

    /**
     * Parse une ligne de log JSON pour l'affichage structuré.
     *
     * @return array{time: string, level: string, message: string, context: array<string, mixed>}|null
     */
    private function parseLogLine(string $line): ?array
    {
        $line = trim($line);

        if ($line === '') {
            return null;
        }

        $data = json_decode($line, true);

        if (! is_array($data)) {
            return [
                'time' => '',
                'level' => $this->lineLevel($line) ?? 'info',
                'message' => $line,
                'context' => [],
            ];
        }

        return [
            'time' => (string) ($data['datetime'] ?? $data['time'] ?? ''),
            'level' => strtolower((string) ($data['level_name'] ?? $data['level'] ?? 'info')),
            'message' => (string) ($data['message'] ?? ''),
            'context' => is_array($data['context'] ?? null) ? $data['context'] : [],
        ];
    }

    /**
     * Retourne le fichier de log le plus récent (rotation quotidienne).
     */
    private function logFilePath(string $source): ?string
    {
        $pattern = $source === 'laravel' ? 'laravel-*.log' : 'activity-*.log';
        $files = glob(storage_path('logs/'.$pattern)) ?: [];

        if ($files !== []) {
            usort($files, fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

            return $files[0];
        }

        $fallback = storage_path('logs/'.($source === 'laravel' ? 'laravel.log' : 'activity.log'));

        return File::exists($fallback) ? $fallback : null;
    }
}
