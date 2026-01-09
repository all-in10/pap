<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\ConfirmsCancelAction;

class EditTimeoff extends EditRecord
{
    use ConfirmsCancelAction;

    protected static string $resource = TimeoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
