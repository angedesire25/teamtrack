<?php

namespace App\Livewire\Club\Teams;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Index extends Component
{
    public bool    $showModal   = false;
    public ?int    $editingId   = null;
    public string  $name        = '';
    public ?int    $category_id = null;
    public ?int    $coach_id    = null;

    // Filtre par catégorie
    public ?int $filterCat = null;

    // Vue effectif
    public ?int $rosterTeamId = null;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'category_id', 'coach_id']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $team = Team::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $team->name;
        $this->category_id = $team->category_id;
        $this->coach_id    = $team->coach_id;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'coach_id'    => 'nullable|exists:users,id',
        ]);

        $data = [
            'name'        => $this->name,
            'category_id' => $this->category_id,
            'coach_id'    => $this->coach_id,
        ];

        if ($this->editingId) {
            Team::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: 'Équipe mise à jour.', type: 'success');
        } else {
            Team::create($data);
            $this->dispatch('toast', message: 'Équipe créée.', type: 'success');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'category_id', 'coach_id']);
    }

    public function delete(int $id): void
    {
        $team = Team::withCount('players')->findOrFail($id);

        if ($team->players_count > 0) {
            $this->dispatch('toast', message: 'Impossible : des joueurs appartiennent à cette équipe.', type: 'error');
            return;
        }

        $team->delete();
        $this->dispatch('toast', message: 'Équipe supprimée.', type: 'success');
    }

    public function toggleRoster(int $teamId): void
    {
        $this->rosterTeamId = ($this->rosterTeamId === $teamId) ? null : $teamId;
    }

    public function render()
    {
        $teamsQuery = Team::with(['category', 'coach'])
            ->withCount('players')
            ->when($this->filterCat, fn($q) => $q->where('category_id', $this->filterCat))
            ->orderBy('name');

        return view('livewire.club.teams.index', [
            'teams'      => $teamsQuery->get(),
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
            'coaches'    => User::where('is_active', true)->orderBy('name')->get(),
            'roster'     => $this->rosterTeamId
                ? Team::with('players.category')->findOrFail($this->rosterTeamId)->players
                : collect(),
        ])->title('Équipes');
    }
}
