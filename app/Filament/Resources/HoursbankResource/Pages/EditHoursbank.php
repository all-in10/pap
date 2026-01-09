<?php

namespace App\Filament\Resources\HoursbankResource\Pages;

use App\Filament\Resources\HoursbankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\ConfirmsCancelAction;

class EditHoursbank extends EditRecord
{
    use ConfirmsCancelAction;

    protected static string $resource = HoursbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
