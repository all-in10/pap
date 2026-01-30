<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\UserRole;

class CentralizedRedirectHandler
{
    /**
     * Middleware centralizado que gerenicia TODOS os redirects da aplicação
     * 
     * Regras:
     * 1. Se não autenticado e tenta acessar painel (/admin, /hr, /employee) → /app/login
     * 2. Se autenticado e tenta acessar /app/login → painel do role
     * 3. Se não autenticado tentou login (flag just_logged_in) → painel do role
     * 4. Se autenticado acessa painel incorreto → redireciona para painel correto
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Diagnostic: log basic request info to help trace redirect loops
        try {
            Log::info('CentralizedRedirectHandler: request start', [
                'path' => $path,
                'method' => $request->method(),
                'session_id' => session()->getId(),
                'cookies' => $request->cookies->all(),
                'auth' => Auth::check(),
                'user_id' => Auth::id(),
                // Dump session data for debugging redirect loop / auth loss
                'session_data_keys' => array_keys(session()->all()),
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors to avoid interfering with request
            Log::error('CentralizedRedirectHandler: failed to log request start', ['error' => (string) $e]);
        }

        // Caso 1: Não autenticado tenta acessar painel
        if (!Auth::check() && $this->isPanelPath($path)) {
            return redirect('/app/login');
        }

        // Se autenticado, processar redirecionamentos
        if (Auth::check()) {
            $user = Auth::user();
            $roleValue = $user->role instanceof UserRole ? $user->role->value : $user->role;
            $targetPanel = $this->getTargetPanel($roleValue);

            Log::info('CentralizedRedirectHandler: authenticated user', [
                'user_id' => $user->id ?? null,
                'role' => $roleValue,
                'path' => $path,
                'target' => $targetPanel,
            ]);

            // Caso 2: Autenticado tenta acessar /app/login (somente GET) → vai para seu painel
            if ($path === 'app/login' && $request->isMethod('get') && ! $request->expectsJson()) {
                Log::info('CentralizedRedirectHandler: redirecting from app/login to panel', ['target' => $targetPanel]);
                return redirect($targetPanel);
            }

            // Caso 3: Autenticado com flag just_logged_in → vai para seu painel
            // Só executar redirecionamento pós-login em GETs idempotentes para não
            // interromper o POST de autenticação e evitar loops de redirect.
            if (session()->has('just_logged_in') && $request->isMethod('get') && ! $request->expectsJson()) {
                Log::info('CentralizedRedirectHandler: handling just_logged_in', ['user_id' => $user->id ?? null, 'path' => $path, 'target' => $targetPanel]);
                session()->forget('just_logged_in');
                // Se ainda não está no painel correto, redireciona
                if (!str_starts_with($path, ltrim($targetPanel, '/'))) {
                    Log::info('CentralizedRedirectHandler: redirecting post-login to panel', ['target' => $targetPanel]);
                    return redirect($targetPanel);
                }

                Log::info('CentralizedRedirectHandler: already on target; no redirect', ['path' => $path, 'target' => $targetPanel]);
            }

            // Caso extra: se o usuário autenticado foi enviado para a raiz do site
            // redirecionar para seu painel (útil quando login redireciona para /)
            $normalizedPath = ltrim($path, '/');
            if (($path === '/' || $normalizedPath === '') && $request->isMethod('get') && ! $request->expectsJson()) {
                if (! str_starts_with($normalizedPath, ltrim($targetPanel, '/'))) {
                    Log::info('CentralizedRedirectHandler: redirecting from root to panel', ['target' => $targetPanel]);
                    return redirect($targetPanel);
                }
            }
        }

        $response = $next($request);

        // Log if a downstream component issued a redirect — helps trace redirect loops
        try {
            if (method_exists($response, 'isRedirection') && $response->isRedirection()) {
                $location = $response->headers->get('Location');
                Log::info('CentralizedRedirectHandler: downstream redirect detected', ['status' => $response->getStatusCode(), 'location' => $location]);
            }
        } catch (\Throwable $e) {
            Log::error('CentralizedRedirectHandler: failed to inspect response', ['error' => (string) $e]);
        }

        return $response;
    }

    /**
     * Verifica se é caminho de painel
     */
    private function isPanelPath(string $path): bool
    {
        return str_starts_with($path, 'admin') ||
            str_starts_with($path, 'hr') ||
            str_starts_with($path, 'employee');
    }

    /**
     * Retorna o painel correto baseado no role
     */
    private function getTargetPanel(string|UserRole $role): string
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }

        if ($role === UserRole::ROOT->value || $role === UserRole::ADMIN->value) {
            return '/admin';
        } elseif ($role === UserRole::HR->value) {
            return '/hr';
        } elseif ($role === UserRole::EMPLOYEE->value) {
            return '/employee';
        }

        return '/app';
    }
}
