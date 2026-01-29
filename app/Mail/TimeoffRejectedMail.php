<?php

namespace App\Mail;

use App\Models\Timeoff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TimeoffRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Timeoff $timeoff, public ?string $reason = null)
    {
    }

    public function envelope(): Envelope
    {
        $employee = $this->timeoff->employee;
        
        return new Envelope(
            from: config('mail.from.address', 'noreply@teamcore.local'),
            subject: "❌ Seu pedido de folga foi rejeitado - {$employee->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.timeoff.rejected',
            with: [
                'timeoff' => $this->timeoff,
                'employee' => $this->timeoff->employee,
                'rejectedBy' => $this->timeoff->approvedBy,
                'daysCount' => $this->timeoff->days_count,
                'startDate' => $this->timeoff->start_date->format('d/m/Y'),
                'endDate' => $this->timeoff->end_date->format('d/m/Y'),
                'type' => $this->timeoff->getTypeLabel(),
                'reason' => $this->reason,
            ],
        );
    }
}
