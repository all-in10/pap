<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract)
    {
    }

    public function envelope(): Envelope
    {
        $employee = $this->contract->employee;
        
        return new Envelope(
            from: config('mail.from.address', 'noreply@teamcore.local'),
            subject: "⚠️ Contrato expirando - {$employee->name}",
        );
    }

    public function content(): Content
    {
        $daysUntilExpiration = now()->diffInDays($this->contract->end_date);
        
        return new Content(
            markdown: 'emails.contract.expiring',
            with: [
                'contract' => $this->contract,
                'employee' => $this->contract->employee,
                'daysUntilExpiration' => $daysUntilExpiration,
                'expiryDate' => $this->contract->end_date->format('d/m/Y'),
                'contractType' => $this->contract->contract_type_name,
                'status' => $this->contract->status,
            ],
        );
    }
}
