<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Access;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Log;

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

        // Authenticated but unauthorized for this resource — redirect to user's panel
        $userRole = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom($user->role);

        if ($userRole === UserRole::ROOT || $userRole === UserRole::ADMIN) {
            Log::warning('EnsureRole: unauthorized access to panel — redirecting to /admin', ['user_id' => $user->id ?? null, 'user_role' => $userRole->value ?? (string) $userRole, 'required_roles' => $roles]);
            return redirect('/admin');
        } elseif ($userRole === UserRole::HR) {
            Log::warning('EnsureRole: unauthorized access to panel — redirecting to /hr', ['user_id' => $user->id ?? null, 'user_role' => $userRole->value ?? (string) $userRole, 'required_roles' => $roles]);
            return redirect('/hr');
        } elseif ($userRole === UserRole::EMPLOYEE) {
            Log::warning('EnsureRole: unauthorized access to panel — redirecting to /employee', ['user_id' => $user->id ?? null, 'user_role' => $userRole->value ?? (string) $userRole, 'required_roles' => $roles]);
            return redirect('/employee');
        }

        abort(403);
    }
}
