<?php

namespace App\Filament\Resources\DesignationResource\Pages;

use App\Filament\Resources\DesignationResource;
use App\Models\Designation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDesignations extends ListRecords
{
    protected static string $resource = DesignationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $u */
                    $u = Auth::user();
                    if ($u === null) {
                        return false;
                    }
                    return $u->can('create', Designation::class);
                }),
        ];
    }
}
