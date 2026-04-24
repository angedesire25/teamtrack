<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class UserAuthActivity extends Notification
{
    public function __construct(
        public readonly string $event,   // 'login' | 'logout'
        public readonly string $userName,
        public readonly string $userEmail,
        public readonly ?string $ip,
        public readonly \DateTimeInterface $occurredAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event'      => $this->event,
            'user_name'  => $this->userName,
            'user_email' => $this->userEmail,
            'ip'         => $this->ip,
            'occurred_at'=> $this->occurredAt->toIso8601String(),
        ];
    }
}
