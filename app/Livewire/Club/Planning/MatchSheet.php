<?php

namespace App\Livewire\Club\Planning;

use App\Models\Event;
use App\Models\EventPlayer;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class MatchSheet extends Component
{
    public Event $event;
    public array $lineup = []; // [player_id => ['lineup' => 'starter'|'substitute'|'none', 'position' => '', 'minutes' => '']] — composition du match par joueur

    public function mount(int $id): void
    {
        $this->event = Event::with(['eventPlayers.player', 'field', 'team'])->findOrFail($id);
        abort_unless($this->event->type === 'match', 404);

        foreach ($this->event->eventPlayers as $ep) {
            $this->lineup[(string) $ep->player_id] = [
                'lineup'   => $ep->lineup,
                'position' => $ep->position_played ?? '',
                'minutes'  => (string) ($ep->minutes_played ?? ''),
            ];
        }
    }

    public function save(): void
    {
        foreach ($this->lineup as $playerId => $data) {
            EventPlayer::where('event_id', $this->event->id)
                ->where('player_id', (int) $playerId)
                ->update([
                    'lineup'          => $data['lineup'],
                    'position_played' => $data['position'] ?: null,
                    'minutes_played'  => $data['minutes'] !== '' ? (int) $data['minutes'] : null,
                ]);
        }

        session()->flash('toast', ['type' => 'success', 'message' => 'Feuille de match enregistrée.']);
        $this->redirect(route('club.planning.match-sheet', $this->event->id), navigate: true);
    }

    public function saveResult(string $scoreHome, string $scoreAway, string $report): void
    {
        $this->event->update([
            'score_home'   => $scoreHome !== '' ? (int) $scoreHome : null,
            'score_away'   => $scoreAway !== '' ? (int) $scoreAway : null,
            'match_report' => $report ?: null,
        ]);

        session()->flash('toast', ['type' => 'success', 'message' => 'Résultat enregistré.']);
        $this->redirect(route('club.planning.match-sheet', $this->event->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.club.planning.match-sheet');
    }
}
