<?php

namespace App\Filament\Resources\StateResource\Pages;

use App\Filament\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateState extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;

    protected static string $resource = StateResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Estado: ' . ($this->record->name ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
