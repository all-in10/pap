<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Renova saldo de férias todo 1º de janeiro
        $schedule->command('vacation:renew-balances')
            ->yearly()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Saldos de férias renovados com sucesso');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Erro ao renovar saldos de férias');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
