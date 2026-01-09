<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateDepartment extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;

    protected static string $resource = DepartmentResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Departamento: ' . ($this->record->name ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
