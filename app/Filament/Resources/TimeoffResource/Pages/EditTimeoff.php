<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class EditTimeoff extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = TimeoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Pedido de Férias ID: ' . $this->record->id . ' atualizado.']);
    }
}
