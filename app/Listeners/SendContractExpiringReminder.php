<?php

namespace App\Listeners;

use App\Events\ContractExpiringReminder;
use App\Mail\ContractExpiringMail;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendContractExpiringReminder implements ShouldQueue
{
    use InteractsWithQueue;

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

        // Enviar email para HR e para o funcionário
        // Email para HR
        $hrUsers = User::where('role', 'hr')->orWhere('role', 'admin')->orWhere('role', 'root')->get();
        foreach ($hrUsers as $user) {
            if ($user->email) {
                Mail::to($user->email)->send(new ContractExpiringMail($contract));
            }
        }

        // Email para o funcionário também
        if ($contract->employee->user->email) {
            Mail::to($contract->employee->user->email)
                ->send(new ContractExpiringMail($contract));
        }
    }
}
