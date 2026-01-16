<?php

namespace App\Filament\Resources\HoursbankResource\Pages;

use App\Filament\Resources\HoursbankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class EditHoursbank extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = HoursbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Banco de Horas ID: ' . $this->record->id . ' atualizado.']);
    }
}
