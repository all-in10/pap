<?php

namespace App\Filament\Traits;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

/**
 * Trait para controlar a visibilidade de widgets baseado no papel (role) do usuário
 * 
 * Uso:
 * 1. Use a trait na classe do widget
 * 2. Implemente o método canView() que retorna true/false
 * 3. Opcionalmente defina allowedRoles() para auto-filtrar
 */
trait WidgetVisibility
{
    /**
     * Define os papéis que podem visualizar este widget
     * Override este método em cada widget
     * 
     * @return array<int, UserRole>
     */
    protected static function allowedRoles(): array
    {
        return [];
    }

    /**
     * Verifica se o usuário atual pode visualizar este widget
     * Implementa lógica de filtro baseada em policies
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        $allowedRoles = static::allowedRoles();

        // Se nenhum role foi definido, mostrar para todos
        if (empty($allowedRoles)) {
            return true;
        }

        // Se há roles definidos, verificar se o usuário tem um deles
        // $user->role pode já ser um enum UserRole, então não precisa de tryFrom()
        $userRole = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom($user->role);
        
        if (!$userRole) {
            return false;
        }

        return in_array($userRole, $allowedRoles, strict: true);
    }
}
