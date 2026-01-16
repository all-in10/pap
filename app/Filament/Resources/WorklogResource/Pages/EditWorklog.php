<?php
declare(strict_types=1);

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\NotifiesUpdatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class EditWorklog extends EditRecord
{
    use NotifiesUpdatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = WorklogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->notifyUpdatedItems(['Registo de Trabalho ID: ' . $this->record->id . ' atualizado.']);
    }
}
