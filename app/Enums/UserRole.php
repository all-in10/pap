<?php

namespace App\Enums;

enum UserRole: string
{
    case ROOT = 'root';
    case ADMIN = 'admin';
    case HR = 'hr';
    case EMPLOYEE = 'employee';

    /**
     * Get all roles in hierarchical order (lower to higher privilege)
     */
    public static function hierarchy(): array
    {
        return [
            self::EMPLOYEE,
            self::HR,
            self::ADMIN,
            self::ROOT,
        ];
    }

    /**
     * Check if this role has at least the privilege level of another role
     */
    public function hasPrivilegeOf(self $other): bool
    {
        $hierarchy = self::hierarchy();
        return array_search($this, $hierarchy, true) >= array_search($other, $hierarchy, true);
    }

    /**
     * Get a user-friendly label for the role
     */
    public function label(): string
    {
        return match ($this) {
            self::ROOT => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::HR => 'Recursos Humanos',
            self::EMPLOYEE => 'Funcionário',
        };
    }

    /**
     * Get roles that can manage a given role
     */
    public static function manageable(): array
    {
        return [
            self::EMPLOYEE->value,
            self::HR->value,
            self::ADMIN->value,
            self::ROOT->value,
        ];
    }
}
