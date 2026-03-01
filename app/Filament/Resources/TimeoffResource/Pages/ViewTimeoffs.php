<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTimeoffs extends ViewRecord
{
    protected static string $resource = TimeoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}