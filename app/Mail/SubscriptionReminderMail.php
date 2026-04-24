<?php

namespace App\Mail;

use App\Models\PlayerSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PlayerSubscription $subscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel de cotisation — ' . ($subscription->player->tenant?->name ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription-reminder',
        );
    }
}
