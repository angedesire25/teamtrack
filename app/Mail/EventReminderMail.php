<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Player $player,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel : ' . $this->event->typeLabel() . ' — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-reminder',
        );
    }
}
