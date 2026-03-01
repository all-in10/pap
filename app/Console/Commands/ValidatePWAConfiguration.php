<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ValidatePWAConfiguration extends Command
{
    protected $signature = 'pwa:validate';

    protected $description = 'Validate PWA configuration and assets';

    public function handle()
    {
        $this->info('═══════════════════════════════════════');
        $this->info('PWA Configuration Validation');
        $this->info('═══════════════════════════════════════');

        $checks = [
            'Service Worker' => $this->checkServiceWorker(),
            'Manifest' => $this->checkManifest(),
            'Icons' => $this->checkIcons(),
            'Middleware' => $this->checkMiddleware(),
            'API Routes' => $this->checkAPIRoutes(),
            'Environment' => $this->checkEnvironment(),
        ];

        $this->printResults($checks);

        $passed = collect($checks)->filter(fn($result) => $result['status'] === 'pass')->count();
        $failed = collect($checks)->filter(fn($result) => $result['status'] === 'fail')->count();

        $this->newLine();
        $this->info("Results: {$passed} passed, {$failed} failed");

        return $failed > 0 ? 1 : 0;
    }

    protected function checkServiceWorker(): array
    {
        $path = public_path('js/sw.js');
        $source = resource_path('js/service-worker.js');

        $exists = File::exists($path);
        $sourceExists = File::exists($source);

        if (!$sourceExists) {
            return [
                'status' => 'fail',
                'message' => 'Service Worker source not found: ' . $source,
            ];
        }

        if (!$exists) {
            return [
                'status' => 'warn',
                'message' => 'Compiled Service Worker not found. Run: npm run build',
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'Service Worker configured at ' . $path,
        ];
    }

    protected function checkManifest(): array
    {
        $path = public_path('manifest.json');

        if (!File::exists($path)) {
            return [
                'status' => 'fail',
                'message' => 'Manifest not found: ' . $path,
            ];
        }

        try {
            $manifest = json_decode(File::get($path), true);

            $required = ['name', 'start_url', 'display', 'icons'];
            $missing = array_diff($required, array_keys($manifest));

            if (!empty($missing)) {
                return [
                    'status' => 'warn',
                    'message' => 'Manifest missing fields: ' . implode(', ', $missing),
                ];
            }

            return [
                'status' => 'pass',
                'message' => 'Manifest valid with ' . count($manifest['icons']) . ' icons',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'message' => 'Manifest invalid JSON: ' . $e->getMessage(),
            ];
        }
    }

    protected function checkIcons(): array
    {
        $iconsDir = public_path('pwa-icons');

        if (!File::isDirectory($iconsDir)) {
            return [
                'status' => 'warn',
                'message' => 'PWA icons directory not found: ' . $iconsDir,
            ];
        }

        $required = [
            'icon-192x192.png',
            'icon-512x512.png',
            'maskable-icon-192x192.png',
        ];

        $found = [];
        $missing = [];

        foreach ($required as $icon) {
            if (File::exists($iconsDir . '/' . $icon)) {
                $found[] = $icon;
            } else {
                $missing[] = $icon;
            }
        }

        if (!empty($missing)) {
            return [
                'status' => 'warn',
                'message' => 'Missing icons: ' . implode(', ', $missing) . ' (' . count($found) . ' found)',
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'All PWA icons present',
        ];
    }

    protected function checkMiddleware(): array
    {
        $path = app_path('Http/Middleware/PWAHeadersMiddleware.php');

        if (!File::exists($path)) {
            return [
                'status' => 'fail',
                'message' => 'PWA Middleware not found: ' . $path,
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'PWA Middleware configured',
        ];
    }

    protected function checkAPIRoutes(): array
    {
        $path = base_path('routes/api.php');

        if (!File::exists($path)) {
            return [
                'status' => 'fail',
                'message' => 'API routes not found: ' . $path,
            ];
        }

        $content = File::get($path);

        if (strpos($content, 'push-subscribe') === false) {
            return [
                'status' => 'warn',
                'message' => 'Push subscription routes not configured',
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'API routes configured',
        ];
    }

    protected function checkEnvironment(): array
    {
        $checks = [];

        // Check for HTTPS in production
        if (app()->environment('production')) {
            if (!config('app.url') || !str_starts_with(config('app.url'), 'https')) {
                return [
                    'status' => 'fail',
                    'message' => 'HTTPS required for production PWA',
                ];
            }
        }

        // Check for queue configuration
        if (config('queue.default') === 'sync') {
            return [
                'status' => 'warn',
                'message' => 'Queue set to sync (use database or redis for production)',
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'Environment configuration OK',
        ];
    }

    protected function printResults(array $checks): void
    {
        foreach ($checks as $name => $result) {
            $status = $result['status'];

            $icon = match ($status) {
                'pass' => '✓',
                'fail' => '✗',
                'warn' => '⚠',
                default => '?',
            };

            $color = match ($status) {
                'pass' => 'info',
                'fail' => 'error',
                'warn' => 'comment',
                default => 'line',
            };

            $this->line(
                $this->formatOutput($icon, $name, $result['message']),
                $color
            );
        }
    }

    protected function formatOutput(string $icon, string $name, string $message): string
    {
        return sprintf(
            '%s %s%s%s',
            $icon,
            str_pad($name . ':', 20),
            PHP_EOL . '  ',
            $message
        );
    }
}
