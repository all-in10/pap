<?php

namespace App\Filament\Resources\FlexibleScheduleResource\Pages;

use App\Filament\Resources\FlexibleScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFlexibleSchedule extends CreateRecord
{
    protected static string $resource = FlexibleScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
