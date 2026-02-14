<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    public function getTitle(): string
    {
        return 'Visualizar Registo de Auditoria';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
