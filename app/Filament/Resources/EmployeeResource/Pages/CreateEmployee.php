<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateEmployee extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        /** @var Employee $employee */
        $employee = $this->record;

        // Refresh relations to ensure any related records created in model events are available
        $employee->refresh();
        $employee->load(['user', 'hoursbank', 'contracts']);

        $created = [];

        if ($employee->user) {
            $created[] = 'Usuário: ' . $employee->user->email;
        }

        if ($employee->hoursbank) {
            $created[] = 'Banco de Horas (ID: ' . $employee->hoursbank->id . ')';
        }

        $contract = $employee->contracts()->latest()->first();
        if ($contract) {
            $created[] = 'Contrato (ID: ' . $contract->id . ', Status: ' . $contract->status . ')';
        }

        $targetUserId = $employee->user?->id ?? null;

        if (!empty($created)) {
            $this->notifyCreatedItems($created, $targetUserId);
        } else {
            $this->notifyCreatedItems(['Funcionário criado com sucesso.'], $targetUserId);
        }
    }
}
