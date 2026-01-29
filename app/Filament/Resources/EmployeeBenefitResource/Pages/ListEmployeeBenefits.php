<?php

namespace App\Filament\Resources\EmployeeBenefitResource\Pages;

use App\Filament\Resources\EmployeeBenefitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeBenefits extends ListRecords
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
