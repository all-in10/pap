<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use App\Models\Timeoff;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimeoffs extends ListRecords
{
    protected static string $resource = TimeoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $u */
                    $u = Auth::user();
                    return $u !== null && $u->can('create', Timeoff::class);
                }),
        ];
    }
}
