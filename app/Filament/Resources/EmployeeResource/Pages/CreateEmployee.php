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

    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        /** @var Employee $employee */
        $employee = $this->record;

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

        if (!empty($created)) {
            $this->notifyCreatedItems($created);
        } else {
            $this->notifyCreatedItems(['Funcionário criado com sucesso.']);
        }
    }
}
