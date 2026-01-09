<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateTimeoff extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = TimeoffResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Solicitação: ' . ($this->record->type ?? '—') . ' para funcionário ID: ' . ($this->record->employee_id ?? '—'),
        ]);
    }
}
