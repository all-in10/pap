<?php

namespace App\Listeners;

use App\Events\TimeoffRejected;
use App\Mail\TimeoffRejectedMail;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CreateTimeoffRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TimeoffRejected $event): void
    {
        $timeoff = $event->timeoff;

        // Criar log de notificação
        NotificationLog::create([
            'user_id' => $timeoff->employee->user_id,
            'title' => 'Solicitação de Folga Rejeitada',
            'body' => sprintf(
                'Sua solicitação de %s de %s até %s foi rejeitada.',
                $timeoff->getTypeLabel(),
                $timeoff->start_date->format('d/m/Y'),
                $timeoff->end_date->format('d/m/Y')
            ),
            'data' => [
                'timeoff_id' => $timeoff->id,
                'type' => $timeoff->type,
                'start_date' => $timeoff->start_date,
                'end_date' => $timeoff->end_date,
            ],
            'is_read' => false,
        ]);

        // Enviar email de rejeição
        if ($timeoff->employee->user->email) {
            Mail::to($timeoff->employee->user->email)
                ->send(new TimeoffRejectedMail($timeoff, $event->reason ?? null));
        }
    }
}
