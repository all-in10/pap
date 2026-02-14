<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Adicionar ações de exportação apenas para admin
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user?->role === 'admin') {
            $actions[] = Action::make('export-csv')
                ->label('📊 Exportar CSV')
                ->color('info')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.csv', 'ActivityLog'));

            $actions[] = Action::make('export-excel')
                ->label('📈 Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.excel', 'ActivityLog'));

            $actions[] = Action::make('export-json')
                ->label('📄 Exportar JSON')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.json', 'ActivityLog'));
        }

        return $actions;
    }

    public function getTitle(): string
    {
        return 'Registos de Auditoria';
    }
}
