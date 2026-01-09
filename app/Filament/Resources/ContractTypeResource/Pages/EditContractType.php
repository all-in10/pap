<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Filament\Resources\ContractTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\ConfirmsCancelAction;

class EditContractType extends EditRecord
{
    use ConfirmsCancelAction;

    protected static string $resource = ContractTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
