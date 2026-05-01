<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $path = storage_path('logs/laravel.log');

        if (!File::exists($path)) {
            return view('admin.logs.index', [
                'logs' => [],
                'stats' => ['total' => 0, 'error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0],
            ]);
        }

        $content = File::get($path);
        $pattern = '/^\[(?<date>.*?)\]\s(?<env>[a-z]+)\.(?<level>[A-Z]+):\s(?<message>.*)$/m';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'date' => $match['date'],
                'env' => $match['env'],
                'level' => strtolower($match['level']),
                'message' => trim($match['message']),
            ];
        }

        $q = trim((string) $request->get('q', ''));
        $level = trim((string) $request->get('level', ''));

        if ($q !== '') {
            $logs = array_values(array_filter(
                $logs,
                fn($log) =>
                stripos($log['message'], $q) !== false
            ));
        }

        if ($level !== '') {
            $logs = array_values(array_filter(
                $logs,
                fn($log) =>
                $log['level'] === $level
            ));
        }

        $stats = [
            'total' => count($logs),
            'error' => count(array_filter($logs, fn($l) => in_array($l['level'], ['error', 'critical', 'alert', 'emergency']))),
            'warning' => count(array_filter($logs, fn($l) => $l['level'] === 'warning')),
            'info' => count(array_filter($logs, fn($l) => in_array($l['level'], ['info', 'notice']))),
            'debug' => count(array_filter($logs, fn($l) => $l['level'] === 'debug')),
        ];

        return view('log', compact('logs', 'stats'));
    }

    public function clear()
    {
        $path = storage_path('logs/laravel.log');

        if (File::exists($path)) {
            File::put($path, '');
        }

        return back()->with('success', 'Log cleared successfully.');
    }
}
