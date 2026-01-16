<?php

namespace App\Filament\Resources\DesignationResource\Pages;

use App\Filament\Resources\DesignationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class EditDesignation extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = DesignationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Designação ID: ' . $this->record->id . ' atualizada.']);
    }
}
