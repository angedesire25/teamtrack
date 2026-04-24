<?php

namespace App\Livewire\Club\Transfers\Incoming;

use App\Models\Transfer;
use App\Models\TransferWindow;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = '';
    public string $search       = '';

    public bool $showModal = false;
    public string $player_name      = '';
    public string $type             = 'permanent';
    public string $counterpart_club = '';
    public string $search_position  = '';
    public string $search_age_min   = '';
    public string $search_age_max   = '';
    public string $search_budget_max = '';
    public string $search_criteria  = '';
    public string $notes            = '';

    protected function rules(): array
    {
        return [
            'player_name'      => 'nullable|string|max:150',
            'type'             => 'required|in:permanent,loan',
            'counterpart_club' => 'nullable|string|max:150',
            'search_position'  => 'nullable|string|max:80',
            'search_age_min'   => 'nullable|integer|min:10|max:50',
            'search_age_max'   => 'nullable|integer|min:10|max:50',
            'search_budget_max'=> 'nullable|integer|min:0',
            'search_criteria'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ];
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['player_name', 'counterpart_club', 'search_position', 'search_age_min', 'search_age_max', 'search_budget_max', 'search_criteria', 'notes']);
        $this->type      = 'permanent';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $tenantId = app('tenant')->id;

        Transfer::create([
            'tenant_id'         => $tenantId,
            'player_name'       => $this->player_name ?: null,
            'direction'         => 'incoming',
            'type'              => $this->type,
            'status'            => 'listed',
            'counterpart_club'  => $this->counterpart_club ?: null,
            'search_position'   => $this->search_position ?: null,
            'search_age_min'    => $this->search_age_min ?: null,
            'search_age_max'    => $this->search_age_max ?: null,
            'search_budget_max' => $this->search_budget_max ?: null,
            'search_criteria'   => $this->search_criteria ?: null,
            'notes'             => $this->notes ?: null,
        ]);

        $this->showModal = false;
    }

    public function render()
    {
        $tenantId = app('tenant')->id;

        $currentWindow = TransferWindow::currentWindow($tenantId);

        $query = Transfer::where('tenant_id', $tenantId)
            ->where('direction', 'incoming')
            ->with('player')
            ->withCount('negotiations');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('player_name', 'like', "%{$this->search}%")
                  ->orWhere('counterpart_club', 'like', "%{$this->search}%")
                  ->orWhereHas('player', fn ($p) => $p->where('first_name', 'like', "%{$this->search}%")
                                                       ->orWhere('last_name', 'like', "%{$this->search}%"));
            });
        }

        $transfers = $query->orderByDesc('updated_at')->paginate(15);

        return view('livewire.club.transfers.incoming.index', compact('transfers', 'currentWindow'));
    }
}
