<?php

namespace App\Livewire\Club\Planning;

use App\Models\Event;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Calendar extends Component
{
    public string $filterTeam = '';

    public function getEventsJson(): string
    {
        $events = Event::with(['field', 'team'])
            ->when($this->filterTeam, fn($q) => $q->where('team_id', $this->filterTeam))
            ->get();

        $data = $events->map(fn(Event $e) => [
            'id'              => $e->id,
            'title'           => $e->title,
            'start'           => $e->starts_at->toIso8601String(),
            'end'             => $e->ends_at->toIso8601String(),
            'color'           => $e->typeColor(),
            'extendedProps'   => [
                'type'     => $e->type,
                'typeLabel'=> $e->typeLabel(),
                'team'     => $e->team?->name,
                'field'    => $e->field?->name,
                'opponent' => $e->opponent,
            ],
        ]);

        return $data->toJson();
    }

    public function render()
    {
        return view('livewire.club.planning.calendar', [
            'eventsJson' => $this->getEventsJson(),
            'teams'      => Team::orderBy('name')->get(),
        ]);
    }
}
