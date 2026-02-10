<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\ChartWidget;
use App\Models\ContractType;
use App\Models\Contract;

class ContractTypeDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Tipos de Contrato';
    protected static ?int $sort = 8;
    protected static ?string $maxContentWidth = 'full';

    protected function getData(): array
    {
        $contractTypes = ContractType::withCount('contracts')
            ->having('contracts_count', '>', 0)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Contratos',
                    'data' => $contractTypes->pluck('contracts_count')->toArray(),
                    'backgroundColor' => [
                        '#582f0e',
                        '#7f4f24',
                        '#936639',
                        '#a68a64',
                        '#b6ad90',
                        '#c2c5aa',
                        '#acb79b',
                    ],
                    'borderColor' => '#333d29',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $contractTypes->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
