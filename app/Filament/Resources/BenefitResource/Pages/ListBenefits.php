<?php

namespace App\Filament\Resources\BenefitResource\Pages;

use App\Filament\Resources\BenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListBenefits extends ListRecords
{
    protected static string $resource = BenefitResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Adicionar ações de exportação apenas para admin/HR
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user?->role !== 'employee') {
            $actions[] = Action::make('export-csv')
                ->label('📊 Exportar CSV')
                ->color('info')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.csv', 'Benefit'));

            $actions[] = Action::make('export-excel')
                ->label('📈 Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.excel', 'Benefit'));

            $actions[] = Action::make('export-json')
                ->label('📄 Exportar JSON')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.json', 'Benefit'));
        }

        return $actions;
    }
}
