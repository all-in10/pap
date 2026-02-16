<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        // Recuperar dados dos itens criados do cache
        $employee = $this->record;
        $userCreated = cache()->get("employee_{$employee->id}_created_user");
        $contractCreated = cache()->get("employee_{$employee->id}_created_contract");
        $hourbankCreated = cache()->get("employee_{$employee->id}_created_hourbank");

        // Mostrar notificações customizadas
        if ($userCreated) {
            Notification::make()
                ->title('Utilizador Criado')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->body("🎉 Utilizador **{$userCreated['email']}** criado com sucesso!\n\n" .
                       "**Role:** {$userCreated['role']}\n" .
                       "**Status:** Deve alterar senha no primeiro acesso")
                ->send();
        }

        if ($contractCreated) {
            Notification::make()
                ->title('Contrato Criado')
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
                ->body("📄 Contrato criado com sucesso!\n\n" .
                       "**Tipo:** {$contractCreated['contract_type']}\n" .
                       "**Salário:** €{$contractCreated['salary']}\n" .
                       "**Data Início:** {$contractCreated['start_date']}")
                ->send();
        }

        if ($hourbankCreated) {
            Notification::make()
                ->title('Banco de Horas Criado')
                ->icon('heroicon-o-clock')
                ->iconColor('success')
                ->body("⏰ Banco de horas criado com sucesso!\n\n" .
                       "**Saldo Inicial:** {$hourbankCreated['balance_hours']}h\n" .
                       "**Data Accrual:** {$hourbankCreated['last_accrual_date']}")
                ->send();
        }

        // Notificação final consolidada
        Notification::make()
            ->title('Employee Criado')
            ->icon('heroicon-o-user-plus')
            ->iconColor('success')
            ->body("✅ Employee **{$employee->first_name} {$employee->last_name}** criado com " .
                   ($userCreated ? '✓ Utilizador ' : '') .
                   ($contractCreated ? '✓ Contrato ' : '') .
                   ($hourbankCreated ? '✓ Banco Horas' : '') .
                   " associados com sucesso!")
            ->send();
    }
}
