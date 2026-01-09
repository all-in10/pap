<?php
declare(strict_types=1);

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\NotifiesCreatedItems;
use App\Traits\SuppressesDefaultFilamentNotifications;

class CreateWorklog extends CreateRecord
{
    use NotifiesCreatedItems;
    use SuppressesDefaultFilamentNotifications;
    use \App\Traits\ConfirmsCancelAction;

    protected static string $resource = WorklogResource::class;

    protected function afterCreate(): void
    {
        $this->notifyCreatedItems([
            'Worklog: ' . ($this->record->work_date?->format('Y-m-d') ?? '—') . ' (Employee ID: ' . ($this->record->employee_id ?? '—') . ')',
        ]);
    }
}
