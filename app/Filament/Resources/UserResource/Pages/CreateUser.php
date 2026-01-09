<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;
use App\Traits\ConfirmsCancelAction;

class CreateUser extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use ConfirmsCancelAction;

    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Usuário: ' . ($this->record->email ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
