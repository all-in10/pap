<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class EditEmployee extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Funcionário ID: ' . $this->record->id . ' atualizado.']);
    }
}
