<?php

namespace App\Filament\Resources\StateResource\Pages;

use App\Filament\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\ConfirmsCancelAction;

class EditState extends EditRecord
{
    use ConfirmsCancelAction;

    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
