<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Filament\Resources\ContractTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateContractType extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = ContractTypeResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Tipo de Contrato: ' . ($this->record->name ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
