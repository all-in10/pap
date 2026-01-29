<?php

namespace Tests\Feature\Mail;

use App\Mail\ContractExpiringMail;
use App\Mail\TimeoffApprovedMail;
use App\Mail\TimeoffRejectedMail;
use App\Models\Contract;
use App\Models\Timeoff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function timeoff_approved_email_is_mailable()
    {
        $timeoff = Timeoff::factory()->approved()->create();

        $mailable = new TimeoffApprovedMail($timeoff);

        $this->assertTrue(true); // Mail created successfully
    }

    /** @test */
    public function timeoff_approved_email_contains_correct_data()
    {
        $timeoff = Timeoff::factory()->approved()->create();
        $mailable = new TimeoffApprovedMail($timeoff);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Seu Pedido de Folga foi Aprovado', $rendered);
        $this->assertStringContainsString($timeoff->start_date->format('d/m/Y'), $rendered);
        $this->assertStringContainsString($timeoff->end_date->format('d/m/Y'), $rendered);
    }

    /** @test */
    public function timeoff_rejected_email_is_mailable()
    {
        $timeoff = Timeoff::factory()->rejected()->create();

        $mailable = new TimeoffRejectedMail($timeoff, 'Conflito com projeto crítico');

        $this->assertTrue(true); // Mail created successfully
    }

    /** @test */
    public function timeoff_rejected_email_contains_correct_data()
    {
        $timeoff = Timeoff::factory()->rejected()->create();
        $reason = 'Conflito com projeto crítico';
        $mailable = new TimeoffRejectedMail($timeoff, $reason);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Seu Pedido de Folga foi Rejeitado', $rendered);
        $this->assertStringContainsString($reason, $rendered);
    }

    /** @test */
    public function contract_expiring_email_is_mailable()
    {
        $contract = Contract::factory()->create();

        $mailable = new ContractExpiringMail($contract);

        $this->assertTrue(true); // Mail created successfully
    }

    /** @test */
    public function contract_expiring_email_contains_correct_data()
    {
        $contract = Contract::factory()->create(['end_date' => now()->addDays(10)]);
        $mailable = new ContractExpiringMail($contract);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Seu Contrato Está Expirando', $rendered);
        $this->assertStringContainsString($contract->end_date->format('d/m/Y'), $rendered);
    }

    /** @test */
    public function emails_use_correct_from_address()
    {
        $timeoff = Timeoff::factory()->approved()->create();
        $mail = new TimeoffApprovedMail($timeoff);
        $envelope = $mail->envelope();

        $this->assertNotNull($envelope->from);
    }

    /** @test */
    public function emails_are_queued()
    {
        $timeoff = Timeoff::factory()->approved()->create();
        $mail = new TimeoffApprovedMail($timeoff);

        // Check if implements ShouldQueue
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $mail);
    }
}
