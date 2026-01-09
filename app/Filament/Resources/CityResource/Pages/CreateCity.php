<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateCity extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;

    protected static string $resource = CityResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Cidade: ' . ($this->record->name ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
