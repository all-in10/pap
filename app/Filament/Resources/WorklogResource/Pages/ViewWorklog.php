<?php

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorklog extends ViewRecord
{
    protected static string $resource = WorklogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}