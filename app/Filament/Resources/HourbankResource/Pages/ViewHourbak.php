<?php

namespace App\Filament\Resources\HourbankResource\Pages;
use App\Filament\Resources\HourbankResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;   

class ViewHourbak extends ViewRecord
{
    protected static string $resource = HourbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}