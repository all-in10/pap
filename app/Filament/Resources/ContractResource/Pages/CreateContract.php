<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class CreateContract extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = ContractResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Contrato criado (ID: ' . ($this->record->id ?? '—') . ', Status: ' . ($this->record->status ?? '—') . ')',
        ]);
    }
}
