<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\ChartWidget;
use App\Models\Contract;

class ContractStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Status dos Contratos';
    protected static ?int $sort = 6;
    protected static ?string $maxContentWidth = 'full';

    protected function getData(): array
    {
        $active = Contract::where('status', 'active')->count();
        $terminated = Contract::where('status', 'terminated')->count();
        $suspended = Contract::where('status', 'suspended')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Quantidade de Contratos',
                    'data' => [
                        $active,
                        $terminated,
                        $suspended,
                    ],
                    'backgroundColor' => [
                        '#c2c5aa', // success - green
                        '#a68a64', // danger - red
                        '#b6ad90', // warning - orange
                    ],
                    'borderColor' => [
                        '#582f0e',
                        '#582f0e',
                        '#582f0e',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Ativos (' . $active . ')',
                'Encerrados (' . $terminated . ')',
                'Suspensos (' . $suspended . ')',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
