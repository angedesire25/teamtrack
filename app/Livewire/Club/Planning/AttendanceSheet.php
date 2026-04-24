<?php

namespace App\Livewire\Club\Planning;

use App\Models\Event;
use App\Models\EventPlayer;
use App\Models\Player;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class AttendanceSheet extends Component
{
    public Event $event;
    public array $attendance = []; // [player_id => 'present'|'absent'|'excused'|'convoked'] — présence par joueur

    public function mount(int $id): void
    {
        $this->event = Event::with(['eventPlayers.player', 'team'])->findOrFail($id);
        abort_unless($this->event->type === 'training', 404);

        foreach ($this->event->eventPlayers as $ep) {
            $this->attendance[(string) $ep->player_id] = $ep->status;
        }
    }

    public function setStatus(int $playerId, string $status): void
    {
        $this->attendance[(string) $playerId] = $status;
    }

    public function save(): void
    {
        foreach ($this->attendance as $playerId => $status) {
            EventPlayer::where('event_id', $this->event->id)
                ->where('player_id', (int) $playerId)
                ->update(['status' => $status]);
        }

        session()->flash('toast', ['type' => 'success', 'message' => 'Présences enregistrées.']);
        $this->redirect(route('club.planning.attendance', $this->event->id), navigate: true);
    }

    public function getStatsProperty(): array
    {
        $total   = count($this->attendance);
        $present = count(array_filter($this->attendance, fn($s) => $s === 'present'));
        $absent  = count(array_filter($this->attendance, fn($s) => $s === 'absent'));
        $excused = count(array_filter($this->attendance, fn($s) => $s === 'excused'));

        return compact('total', 'present', 'absent', 'excused');
    }

    public function render()
    {
        return view('livewire.club.planning.attendance-sheet', [
            'players' => $this->event->eventPlayers->sortBy('player.last_name'),
            'stats'   => $this->getStatsProperty(),
        ]);
    }
}
