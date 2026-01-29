<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\ChartWidget;
use App\Models\Contract;
use App\Models\ContractType;

class ContractsChart extends ChartWidget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Contratos por Tipo';

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
            UserRole::HR,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $contractTypes = ContractType::withCount('contracts')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Número de Contratos',
                    'data' => $contractTypes->pluck('contracts_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)', // primary blue
                        'rgba(16, 185, 129, 0.8)', // success green
                        'rgba(245, 158, 11, 0.8)', // warning amber
                        'rgba(239, 68, 68, 0.8)',  // danger red
                        'rgba(139, 92, 246, 0.8)', // violet
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $contractTypes->pluck('name')->toArray(),
        ];
    }
}