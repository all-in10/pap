<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Filament\Resources\ContractTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class EditContractType extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = ContractTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Tipo de Contrato ID: ' . $this->record->id . ' atualizado.']);
    }
}
