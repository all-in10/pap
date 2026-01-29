<?php

namespace App\Filament\Resources\FlexibleScheduleResource\Pages;

use App\Filament\Resources\FlexibleScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFlexibleSchedules extends ListRecords
{
    protected static string $resource = FlexibleScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
