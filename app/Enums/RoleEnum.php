<?php

namespace App\Enums;

/**
 * Role Enum - Define os tipos de papel/cargo dos usuários
 * 
 * Esta classe fornece constantes para os diferentes papéis no sistema
 * e métodos auxiliares para verificação de autorização.
 */
class RoleEnum
{
    /**
     * Papel de Administrador
     * Acesso total ao sistema
     */
    public const ADMIN = 'admin';

    /**
     * Papel de Recursos Humanos
     * Pode gerenciar pessoas e aprovar pedidos
     */
    public const HR = 'hr';

    /**
     * Papel de Funcionário
     * Acesso básico ao sistema
     */
    public const EMPLOYEE = 'employee';

    /**
     * Lista de todos os papéis disponíveis
     */
    public const ALL = [
        self::ADMIN,
        self::HR,
        self::EMPLOYEE,
    ];

    /**
     * Rótulos legíveis para cada papel
     */
    public const LABELS = [
        self::ADMIN => 'Administrador',
        self::HR => 'Recursos Humanos',
        self::EMPLOYEE => 'Funcionário',
    ];

    /**
     * Verifica se um papel é aprovador (pode aprovar pedidos)
     * 
     * @param string $role
     * @return bool
     */
    public static function isApprover(string $role): bool
    {
        return in_array($role, [self::ADMIN, self::HR]);
    }

    /**
     * Verifica se um papel é adminstrador
     * 
     * @param string $role
     * @return bool
     */
    public static function isAdmin(string $role): bool
    {
        return $role === self::ADMIN;
    }

    /**
     * Verifica se um papel é HR
     * 
     * @param string $role
     * @return bool
     */
    public static function isHR(string $role): bool
    {
        return $role === self::HR;
    }

    /**
     * Verifica se um papel é Funcionário
     * 
     * @param string $role
     * @return bool
     */
    public static function isEmployee(string $role): bool
    {
        return $role === self::EMPLOYEE;
    }

    /**
     * Obtém o rótulo legível para um papel
     * 
     * @param string $role
     * @return string
     */
    public static function getLabel(string $role): string
    {
        return self::LABELS[$role] ?? $role;
    }
}
