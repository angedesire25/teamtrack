<?php

namespace App\Livewire\Club;

use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function deleteAll(): void
    {
        auth()->user()->notifications()->delete();
    }

    public function render()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(20)
            ->get();

        return view('livewire.club.notification-bell', [
            'notifications' => $notifications,
            'unreadCount'   => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
