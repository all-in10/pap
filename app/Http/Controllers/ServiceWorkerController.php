<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class ServiceWorkerController extends Controller
{
    /**
     * Serve the Service Worker file with correct security headers
     */
    public function serve()
    {
        // Try multiple possible locations
        $possiblePaths = [
            public_path('build/js/sw.js'),
            public_path('js/sw.js'),
            base_path('resources/js/service-worker.js'),
        ];

        $swPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $swPath = $path;
                Log::info('[PWA] Service Worker found at: ' . $path);
                break;
            }
        }

        if (!$swPath) {
            Log::error('[PWA] Service Worker not found in any location');
            return response('Service Worker not found', 404, [
                'Content-Type' => 'text/plain',
            ]);
        }

        $content = file_get_contents($swPath);

        return response($content, 200, [
            'Cache-Control' => 'public, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Service-Worker-Allowed' => '/',
            'Content-Type' => 'application/javascript; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
