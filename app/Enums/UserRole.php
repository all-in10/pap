<?php

namespace App\Enums;

enum UserRole: string
{
    case ROOT = 'root';
    case ADMIN = 'admin';
    case HR = 'hr';
    case EMPLOYEE = 'employee';

    /**
     * Retorna todas as roles em ordem hierárquica (menor para maior privilégio)
     * Ordem: EMPLOYEE -> HR -> ADMIN -> ROOT
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
     * Verifica se esta role tem pelo menos o nível de privilégio de outra role
     * Baseado na hierarquia: roles superiores podem acessar recursos de roles inferiores
     */
    public function hasPrivilegeOf(self $other): bool
    {
        $hierarchy = self::hierarchy();
        return array_search($this, $hierarchy, true) >= array_search($other, $hierarchy, true);
    }

    /**
     * Retorna um rótulo amigável para a role em português
     * Usado na interface para exibir nomes legíveis das roles
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
     * Retorna as roles que podem ser gerenciadas por outras roles
     * Usado para determinar quais roles um usuário pode administrar
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
