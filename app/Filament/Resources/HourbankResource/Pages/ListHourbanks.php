<?php

namespace App\Filament\Resources\HourbankResource\Pages;

use App\Filament\Resources\HourbankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHourbanks extends ListRecords
{
    protected static string $resource = HourbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
