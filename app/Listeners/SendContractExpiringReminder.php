<?php

namespace App\Listeners;

use App\Events\ContractExpiringReminder;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendContractExpiringReminder
{
    /**
     * Handle the event.
     */
    public function handle(ContractExpiringReminder $event): void
    {
        $contract = $event->contract;
        $daysUntilExpiry = $event->daysUntilExpiry;

        // Criar log de notificação para HR
        NotificationLog::create([
            'user_id' => null, // será lido por HR users
            'type' => 'contract_expiring',
            'title' => 'Contrato Vencendo em Breve',
            'message' => sprintf(
                'O contrato de %s (%s) vence em %d dias (%s).',
                $contract->employee->first_name . ' ' . $contract->employee->last_name,
                $contract->employee->designation?->name ?? 'N/A',
                $daysUntilExpiry,
                $contract->end_date->format('d/m/Y')
            ),
            'data' => [
                'contract_id' => $contract->id,
                'employee_id' => $contract->employee_id,
                'end_date' => $contract->end_date,
                'days_until_expiry' => $daysUntilExpiry,
            ],
            'is_read' => false,
        ]);

        // TODO: Enviar email para HR quando SMTP estiver configurado
    }
}
