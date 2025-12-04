<?php
declare(strict_types=1);

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use App\Models\Worklog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListWorklogs extends ListRecords
{
    protected static string $resource = WorklogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $u */
                    $u = Auth::user();
                    return $u !== null && $u->can('create', Worklog::class);
                }),
        ];
    }
}
