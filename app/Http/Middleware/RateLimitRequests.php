<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;

class RateLimitRequests
{
    public function __construct(protected RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Aplicar rate limits baseado na rota
        if ($this->isLoginAttempt($request)) {
            return $this->handleLoginRateLimit($request, $next);
        }

        if ($this->isPasswordResetAttempt($request)) {
            return $this->handlePasswordResetRateLimit($request, $next);
        }

        if ($this->isApiRequest($request)) {
            return $this->handleApiRateLimit($request, $next);
        }

        return $next($request);
    }

    /**
     * Verifica se é uma tentativa de login
     */
    private function isLoginAttempt(Request $request): bool
    {
        return $request->path() === 'login' && $request->isMethod('post');
    }

    /**
     * Verifica se é uma tentativa de reset de senha
     */
    private function isPasswordResetAttempt(Request $request): bool
    {
        return $request->path() === 'password/update' && $request->isMethod('post');
    }

    /**
     * Verifica se é uma requisição API
     */
    private function isApiRequest(Request $request): bool
    {
        return $request->path() === 'api' || str_starts_with($request->path(), 'api/');
    }

    /**
     * Limita tentativas de login a 5 por 5 minutos
     */
    private function handleLoginRateLimit(Request $request, Closure $next): Response
    {
        $key = 'login_' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 5;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.',
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }

    /**
     * Limita reset de senha a 3 por hora
     */
    private function handlePasswordResetRateLimit(Request $request, Closure $next): Response
    {
        $key = 'password_reset_' . $request->ip();
        $maxAttempts = 3;
        $decayMinutes = 60;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many password reset attempts. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.',
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }

    /**
     * Limita API requests a 60 por minuto (por IP ou user_id)
     */
    private function handleApiRateLimit(Request $request, Closure $next): Response
    {
        // Se autenticado, usar user_id; senão, usar IP
        $key = $request->user() 
            ? 'api_' . $request->user()->id 
            : 'api_' . $request->ip();

        $maxAttempts = 60;
        $decayMinutes = 1;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Rate limit exceeded. Maximum ' . $maxAttempts . ' requests per ' . $decayMinutes . ' minute.',
                'retry_after' => $this->limiter->availableIn($key),
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);
        
        // Adicionar headers de rate limit
        return $response
            ->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', max(0, $maxAttempts - $this->limiter->attempts($key)))
            ->header('X-RateLimit-Reset', now()->addSeconds($decayMinutes * 60)->timestamp);
    }
}
