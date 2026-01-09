<?php
declare(strict_types=1);

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\ConfirmsCancelAction;

class EditWorklog extends EditRecord
{
    use ConfirmsCancelAction;

    protected static string $resource = WorklogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
