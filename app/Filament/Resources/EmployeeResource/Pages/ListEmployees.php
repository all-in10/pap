<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Adicionar ações de exportação apenas para admin/HR
        if (auth()->user()?->role !== 'employee') {
            $actions[] = Action::make('export-csv')
                ->label('📊 Exportar CSV')
                ->color('info')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.csv', 'Employee'));

            $actions[] = Action::make('export-excel')
                ->label('📈 Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.excel', 'Employee'));

            $actions[] = Action::make('export-json')
                ->label('📄 Exportar JSON')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.json', 'Employee'));
        }

        return $actions;
    }
}
