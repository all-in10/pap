<?php

namespace App\Filament\Resources\HoursbankResource\Pages;

use App\Filament\Resources\HoursbankResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateHoursbank extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;

    protected static string $resource = HoursbankResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Banco de Horas criado (ID: ' . ($this->record->id ?? '—') . ') para funcionário ID: ' . ($this->record->employee_id ?? '—'),
        ]);
    }
}
