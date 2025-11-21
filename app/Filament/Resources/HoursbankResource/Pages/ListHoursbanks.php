<?php

namespace App\Filament\Resources\HoursbankResource\Pages;

use App\Filament\Resources\HoursbankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHoursbanks extends ListRecords
{
    protected static string $resource = HoursbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
