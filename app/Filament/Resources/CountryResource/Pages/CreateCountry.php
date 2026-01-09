<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateCountry extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = CountryResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'País: ' . ($this->record->name ?? '—') . ' (ID: ' . ($this->record->id ?? '—') . ')',
        ]);
    }
}
