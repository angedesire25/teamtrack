<?php

namespace App\Livewire\Club\Transfers\Outgoing;

use App\Models\Player;
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
    public int  $playerId  = 0;
    public string $type         = 'permanent';
    public string $asking_price = '';
    public string $counterpart_club = '';
    public string $notes        = '';

    protected function rules(): array
    {
        return [
            'playerId'        => 'required|exists:players,id',
            'type'            => 'required|in:permanent,loan',
            'asking_price'    => 'nullable|integer|min:0',
            'counterpart_club'=> 'nullable|string|max:150',
            'notes'           => 'nullable|string',
        ];
    }

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['playerId', 'type', 'asking_price', 'counterpart_club', 'notes']);
        $this->type      = 'permanent';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $tenantId = app('tenant')->id;

        Transfer::create([
            'tenant_id'        => $tenantId,
            'player_id'        => $this->playerId,
            'direction'        => 'outgoing',
            'type'             => $this->type,
            'status'           => 'listed',
            'asking_price'     => $this->asking_price ?: null,
            'counterpart_club' => $this->counterpart_club ?: null,
            'notes'            => $this->notes ?: null,
        ]);

        $this->showModal = false;
    }

    public function render()
    {
        $tenantId = app('tenant')->id;

        $currentWindow = TransferWindow::currentWindow($tenantId);

        $query = Transfer::where('tenant_id', $tenantId)
            ->where('direction', 'outgoing')
            ->with('player')
            ->withCount('negotiations');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('player', fn ($p) => $p->where('first_name', 'like', "%{$this->search}%")
                                                     ->orWhere('last_name', 'like', "%{$this->search}%"))
                  ->orWhere('counterpart_club', 'like', "%{$this->search}%");
            });
        }

        $transfers = $query->orderByDesc('updated_at')->paginate(15);

        $players = Player::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'injured', 'suspended'])
            ->orderBy('last_name')
            ->get();

        return view('livewire.club.transfers.outgoing.index', compact('transfers', 'players', 'currentWindow'));
    }
}
