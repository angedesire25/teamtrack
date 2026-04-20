<?php

namespace App\Mail;

use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation) {}

    public function envelope(): Envelope
    {
        $tenant = \App\Models\Tenant::find($this->donation->tenant_id);
        return new Envelope(
            subject: 'Votre reçu de don — ' . ($tenant?->name ?? 'Club'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.donation-receipt');
    }

    public function attachments(): array
    {
        $donation = $this->donation;
        $tenant   = \App\Models\Tenant::find($donation->tenant_id);

        $pdf = Pdf::loadView('pdf.donation-receipt', compact('donation', 'tenant'))
            ->setPaper('a5');

        return [
            Attachment::fromData(fn () => $pdf->output(), 'recu-don-' . $donation->receipt_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
