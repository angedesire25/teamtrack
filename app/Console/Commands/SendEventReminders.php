<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Event;
use App\Models\EventPlayer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send email reminders to convoked players 24h before each event';

    public function handle(): void
    {
        $window_start = now()->addHours(23)->addMinutes(55);
        $window_end   = now()->addHours(24)->addMinutes(5);

        $events = Event::withoutGlobalScope('tenant')
            ->whereBetween('starts_at', [$window_start, $window_end])
            ->whereIn('type', ['match', 'training'])
            ->get();

        foreach ($events as $event) {
            $players = EventPlayer::where('event_id', $event->id)
                ->where('status', 'convoked')
                ->with('player')
                ->get();

            foreach ($players as $ep) {
                $player = $ep->player;
                if ($player && $player->email) {
                    Mail::to($player->email)->send(new EventReminderMail($event, $player));
                    $this->line("Reminder sent to {$player->first_name} {$player->last_name} for event #{$event->id}");
                }
            }
        }

        $this->info("Done. Processed {$events->count()} event(s).");
    }
}
