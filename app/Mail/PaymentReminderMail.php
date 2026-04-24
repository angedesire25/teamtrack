<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string     $clubName,
        public readonly int        $amountDue,
        public readonly int        $daysOverdue,
        public readonly Collection $pendingPayments,
        public readonly ?string    $note = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Relance de paiement — TeamTrack');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-reminder');
    }
}
