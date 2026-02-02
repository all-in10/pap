<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeoffs extends ListRecords
{
    protected static string $resource = TimeoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
