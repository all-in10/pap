<?php

namespace App\Filament\Resources\EmployeeBenefitResource\Pages;

use App\Filament\Resources\EmployeeBenefitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeBenefit extends CreateRecord
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
