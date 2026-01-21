<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Access;

/**
 * Middleware para garantir que o usuário tenha uma das roles especificadas
 * Uso em rotas: ->middleware("role:admin,hr")
 * Usuários ROOT passam por todas as verificações
 */

class EnsureRole
{
    /**
     * Processa a requisição verificando se o usuário tem as roles necessárias
     * Se não autenticado, deixa o middleware de auth lidar com o redirecionamento
     * Se ROOT, permite acesso direto
     * Caso contrário, verifica se tem alguma das roles especificadas
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            // not authenticated — let auth middleware handle redirect
            return $next($request);
        }

        // Root bypass
        if (Access::hasRole('root', $user)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if (Access::hasRole($role, $user)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
