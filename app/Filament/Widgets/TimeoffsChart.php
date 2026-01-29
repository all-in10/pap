<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\ChartWidget;
use App\Models\Timeoff;

class TimeoffsChart extends ChartWidget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Tipos de Solicitações de licenças';

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
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];
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
                        'rgba(107, 114, 128, 0.8)', // cancelled - gray
                    ],
                    'borderColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(107, 114, 128)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pendente', 'Aprovado', 'Rejeitado', 'Cancelado'],
        ];
    }
}