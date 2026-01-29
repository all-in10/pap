<?php

namespace App\Listeners;

use App\Events\TimeoffApproved;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTimeoffApprovedNotification
{
    /**
     * Handle the event.
     */
    public function handle(TimeoffApproved $event): void
    {
        $timeoff = $event->timeoff;

        // Criar log de notificação
        NotificationLog::create([
            'user_id' => $timeoff->employee->user_id,
            'title' => 'Solicitação de Folga Aprovada',
            'body' => sprintf(
                'Sua solicitação de %s de %s até %s foi aprovada.',
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

        // TODO: Enviar email quando SMTP estiver configurado
        // Mail::to($timeoff->employee->user->email)->send(new TimeoffApprovedMail($timeoff));
    }
}
