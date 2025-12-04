<?php

namespace App\Filament\Resources\HoursbankResource\Pages;

use App\Filament\Resources\HoursbankResource;
use App\Models\Hoursbank;
use App\Enums\UserRole;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListHoursbanks extends ListRecords
{
    protected static string $resource = HoursbankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $u */
                    $u = Auth::user();
                    return $u !== null && $u->can('create', Hoursbank::class);
                }),
        ];
    }

    // Filtering moved to the Resource::table() via modifyQueryUsing().
}
