<?php

namespace App\Filament\Resources\EmployeeBenefitResource\Pages;

use App\Filament\Resources\EmployeeBenefitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeBenefit extends EditRecord
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
