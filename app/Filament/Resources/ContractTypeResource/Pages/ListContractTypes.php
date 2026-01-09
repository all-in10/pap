<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Filament\Resources\ContractTypeResource;
use App\Models\ContractType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListContractTypes extends ListRecords
{
    protected static string $resource = ContractTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    return ContractTypeResource::canCreate();
                }),
        ];
    }
}
