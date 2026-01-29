<?php

namespace App\Filament\Resources\FlexibleScheduleResource\Pages;

use App\Filament\Resources\FlexibleScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFlexibleSchedule extends EditRecord
{
    protected static string $resource = FlexibleScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
