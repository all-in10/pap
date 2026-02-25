<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Action para exportar dados em formato HTML/CSV via download
 * Pode ser usado em bulk actions ou como header action
 */
class ExportAction extends Action
{
    protected string $format = 'csv'; // csv, excel, json

    public function __construct(string $format = 'csv')
    {
        parent::__construct();
        $this->format = $format;
    }

    public static function exportCSV(): self
    {
        return (new self('csv'))
            ->label('Exportar CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('info');
    }

    public static function exportExcel(): self
    {
        return (new self('excel'))
            ->label('Exportar Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success');
    }

    public static function exportJSON(): self
    {
        return (new self('json'))
            ->label('Exportar JSON')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('warning');
    }
}

/**
 * BulkAction para exportação em bulk
 */
class ExportBulkAction extends BulkAction
{
    protected string $format = 'csv';

    public function __construct(string $format = 'csv')
    {
        parent::__construct();
        $this->format = $format;
    }

    public static function exportCSV(): self
    {
        $action = new self('csv');
        $action->label = 'Exportar CSV';
        $action->icon = 'heroicon-o-arrow-down-tray';
        $action->color = 'info';
        return $action;
    }

    public static function exportExcel(): self
    {
        $action = new self('excel');
        $action->label = 'Exportar Excel';
        $action->icon = 'heroicon-o-arrow-down-tray';
        $action->color = 'success';
        return $action;
    }

    public static function exportJSON(): self
    {
        $action = new self('json');
        $action->label = 'Exportar JSON';
        $action->icon = 'heroicon-o-arrow-down-tray';
        $action->color = 'warning';
        return $action;
    }
}
