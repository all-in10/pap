<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Timeoff;

class TimeoffsChart extends ChartWidget
{
    protected static ?string $heading = 'Estado de aprovaçãode licenças';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $data = [];

        foreach ($statuses as $status) {
            $data[] = Timeoff::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Número de Solicitações',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.8)',  // pending - warning amber
                        'rgba(16, 185, 129, 0.8)',  // approved - success green
                        'rgba(239, 68, 68, 0.8)',   // rejected - danger red
                    ],
                    'borderColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pendente', 'Aprovado', 'Rejeitado',]
        ];
    }
}