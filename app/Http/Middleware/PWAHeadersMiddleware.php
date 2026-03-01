<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PWA Headers Middleware
 * 
 * Adiciona headers HTTP necessários para PWA funcionar corretamente:
 * - Service-Worker-Allowed: permite SW em root
 * - Cache-Control: estratégia de cache adequada
 * - Content-Type: corretos para manifestos e SW
 */
class PWAHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Service Worker: nunca cachear, validar sempre
        if ($request->path() === 'js/sw.js' || preg_match('/build\/js\/sw\.js$/i', $request->path())) {
            $response->headers->set('Cache-Control', 'public, no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Service-Worker-Allowed', '/');
            $response->headers->set('Content-Type', 'application/javascript; charset=utf-8');
        }

        // Manifest: cache por 1 dia
        if ($request->path() === 'manifest.json') {
            $response->headers->set('Cache-Control', 'public, max-age=86400');
            $response->headers->set('Content-Type', 'application/manifest+json; charset=utf-8');
        }

        // Assets estáticos (CSS, JS, fonts): cache longo
        if (preg_match('/\.(js|css|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|webp)$/i', $request->path())) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        // PWA Icons: cache by 30 days
        if (preg_match('/pwa-icons\//i', $request->path())) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000');
        }

        // HTML páginas: cache curto (1 hora) para PWA update check
        if ($request->path() === '/' || 
            $request->path() === '/offline' ||
            preg_match('/\.html?$/i', $request->path())) {
            $response->headers->set('Cache-Control', 'public, max-age=3600, must-revalidate');
        }

        // API responses: sem cache (sempre fresh)
        if (preg_match('/^api\//i', $request->path())) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // Headers de segurança para PWA
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
