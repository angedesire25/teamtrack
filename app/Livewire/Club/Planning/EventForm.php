<?php

namespace App\Livewire\Club\Planning;

use App\Mail\EventReminderMail;
use App\Models\Event;
use App\Models\EventPlayer;
use App\Models\Field;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.club')]
class EventForm extends Component
{
    public ?int $eventId = null;
    public Event $event;

    public string $type        = 'training';
    public string $title       = '';
    public string $starts_at   = '';
    public string $ends_at     = '';
    public string $field_id    = '';
    public string $team_id     = '';
    public string $notes       = '';

    // Récurrence
    public bool   $is_recurring      = false;
    public string $recurrence_until  = '';

    // Données spécifiques aux matchs
    public string $competition = '';
    public string $opponent    = '';
    public string $home_away   = 'home';
    public string $score_home  = '';
    public string $score_away  = '';
    public string $match_report = '';

    // Convocations
    public array $convokedPlayerIds = [];
    public bool  $sendConvocationEmails = false;

    protected function rules(): array
    {
        return [
            'type'      => 'required|in:match,training,meeting,travel',
            'title'     => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after:starts_at',
            'field_id'  => 'nullable|exists:fields,id',
            'team_id'   => 'nullable|exists:teams,id',
            'is_recurring'     => 'boolean',
            'recurrence_until' => 'nullable|date|after:starts_at',
            'competition'  => 'nullable|string|max:255',
            'opponent'     => 'nullable|string|max:255',
            'home_away'    => 'nullable|in:home,away,neutral',
            'score_home'   => 'nullable|integer|min:0',
            'score_away'   => 'nullable|integer|min:0',
            'match_report' => 'nullable|string',
            'notes'        => 'nullable|string',
            'convokedPlayerIds' => 'array',
            'convokedPlayerIds.*' => 'exists:players,id',
        ];
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->eventId = $id;
            $event = Event::with('eventPlayers')->findOrFail($id);
            $this->fill([
                'type'              => $event->type,
                'title'             => $event->title,
                'starts_at'         => $event->starts_at->format('Y-m-d\TH:i'),
                'ends_at'           => $event->ends_at->format('Y-m-d\TH:i'),
                'field_id'          => (string) ($event->field_id ?? ''),
                'team_id'           => (string) ($event->team_id ?? ''),
                'notes'             => $event->notes ?? '',
                'is_recurring'      => $event->is_recurring,
                'recurrence_until'  => $event->recurrence_until?->format('Y-m-d') ?? '',
                'competition'       => $event->competition ?? '',
                'opponent'          => $event->opponent ?? '',
                'home_away'         => $event->home_away ?? 'home',
                'score_home'        => (string) ($event->score_home ?? ''),
                'score_away'        => (string) ($event->score_away ?? ''),
                'match_report'      => $event->match_report ?? '',
            ]);
            $this->convokedPlayerIds = $event->eventPlayers->pluck('player_id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $now = now();
            $this->starts_at = $now->format('Y-m-d\TH:i');
            $this->ends_at   = $now->addHour()->format('Y-m-d\TH:i');
        }
    }

    public function updatedType(): void
    {
        if ($this->type === 'training') {
            $this->title = 'Entraînement';
        } elseif ($this->type === 'match') {
            $this->title = 'Match';
        }
    }

    public function togglePlayer(int $playerId): void
    {
        $key = (string) $playerId;
        if (in_array($key, $this->convokedPlayerIds)) {
            $this->convokedPlayerIds = array_values(array_filter($this->convokedPlayerIds, fn($id) => $id !== $key));
        } else {
            $this->convokedPlayerIds[] = $key;
        }
    }

    public function selectAllPlayers(): void
    {
        $this->convokedPlayerIds = Player::when($this->team_id, fn($q) => $q->where('team_id', $this->team_id))
            ->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function clearPlayers(): void
    {
        $this->convokedPlayerIds = [];
    }

    public function save(): void
    {
        $data = $this->validate();

        $eventData = [
            'type'       => $data['type'],
            'title'      => $data['title'],
            'starts_at'  => $data['starts_at'],
            'ends_at'    => $data['ends_at'],
            'field_id'   => $data['field_id'] ?: null,
            'team_id'    => $data['team_id'] ?: null,
            'notes'      => $data['notes'] ?: null,
            'is_recurring'     => $data['is_recurring'],
            'recurrence_until' => ($data['is_recurring'] && $data['recurrence_until']) ? $data['recurrence_until'] : null,
            'competition'  => $data['competition'] ?: null,
            'opponent'     => $data['opponent'] ?: null,
            'home_away'    => $data['home_away'] ?: null,
            'score_home'   => $data['score_home'] !== '' ? (int)$data['score_home'] : null,
            'score_away'   => $data['score_away'] !== '' ? (int)$data['score_away'] : null,
            'match_report' => $data['match_report'] ?: null,
        ];

        if ($this->eventId) {
            $event = Event::findOrFail($this->eventId);
            $event->update($eventData);
        } else {
            $event = Event::create($eventData);

            // Générer les occurrences récurrentes pour les entraînements
            if ($event->is_recurring && $event->recurrence_until && $event->type === 'training') {
                $this->generateRecurrences($event);
            }
        }

        // Synchroniser les joueurs convoqués
        $this->syncConvocations($event, $data['convokedPlayerIds'] ?? []);

        // Envoyer les e-mails de convocation si demandé
        if ($this->sendConvocationEmails && !$event->convocations_sent) {
            $this->sendEmails($event);
            $event->update(['convocations_sent' => true]);
        }

        session()->flash('toast', ['type' => 'success', 'message' => $this->eventId ? 'Événement modifié.' : 'Événement créé.']);
        $this->redirect(route('club.planning.calendar'), navigate: true);
    }

    private function generateRecurrences(Event $parent): void
    {
        $current = Carbon::parse($parent->starts_at)->addWeek();
        $until   = Carbon::parse($parent->recurrence_until)->endOfDay();
        $duration = Carbon::parse($parent->starts_at)->diffInMinutes(Carbon::parse($parent->ends_at));

        while ($current->lte($until)) {
            Event::create(array_merge($parent->only([
                'tenant_id', 'type', 'title', 'field_id', 'team_id', 'notes',
            ]), [
                'starts_at'       => $current->copy(),
                'ends_at'         => $current->copy()->addMinutes($duration),
                'is_recurring'    => true,
                'parent_event_id' => $parent->id,
            ]));
            $current->addWeek();
        }
    }

    private function syncConvocations(Event $event, array $playerIds): void
    {
        $existing = $event->eventPlayers()->pluck('player_id')->map(fn($id) => (string)$id)->toArray();
        $new      = array_map('strval', $playerIds);

        // Supprimer les joueurs décochés
        $toRemove = array_diff($existing, $new);
        if ($toRemove) {
            EventPlayer::where('event_id', $event->id)->whereIn('player_id', $toRemove)->delete();
        }

        // Ajouter les nouveaux
        foreach (array_diff($new, $existing) as $playerId) {
            EventPlayer::firstOrCreate([
                'event_id'  => $event->id,
                'player_id' => (int) $playerId,
            ], ['status' => 'convoked', 'lineup' => 'none']);
        }
    }

    private function sendEmails(Event $event): void
    {
        $eventPlayers = $event->eventPlayers()->with('player')->where('status', 'convoked')->get();
        foreach ($eventPlayers as $ep) {
            if ($ep->player && $ep->player->email) {
                Mail::to($ep->player->email)->send(new EventReminderMail($event, $ep->player));
            }
        }
    }

    public function delete(): void
    {
        if ($this->eventId) {
            Event::findOrFail($this->eventId)->delete();
            session()->flash('toast', ['type' => 'success', 'message' => 'Événement supprimé.']);
        }
        $this->redirect(route('club.planning.calendar'), navigate: true);
    }

    public function render()
    {
        $players = Player::when($this->team_id, fn($q) => $q->where('team_id', $this->team_id))
            ->orderBy('last_name')
            ->get();

        return view('livewire.club.planning.event-form', [
            'fields'  => Field::where('is_active', true)->orderBy('name')->get(),
            'teams'   => Team::orderBy('name')->get(),
            'players' => $players,
        ]);
    }
}
