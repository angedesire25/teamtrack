<?php

namespace App\Livewire\Club\Stock\Jerseys;

use App\Models\Jersey;
use App\Models\JerseyAssignment;
use App\Models\Player;
use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url] public string $filterType   = '';
    #[Url] public string $filterSeason = '';
    #[Url] public string $search       = '';
    #[Url] public string $tab          = 'stock'; // stock | assignments — onglet actif

    // Modal catalogue
    public bool   $showModal = false;
    public ?int   $editingId = null;
    public string $name     = '';
    public string $type     = 'home';
    public string $season   = '';
    public string $color    = '';
    public string $size     = 'L';
    public string $quantity_total     = '0';
    public string $quantity_available = '0';
    public string $low_stock_threshold = '2';
    public string $unit_price  = '';
    public string $supplier_id = '';
    public string $notes       = '';

    // Modal attribution
    public bool   $showAssignModal = false;
    public ?int   $assignJerseyId  = null;
    public string $assignPlayerId  = '';
    public string $assignNumber    = '';
    public string $assignSeason    = '';
    public string $assignDate      = '';

    // Modal retour
    public bool   $showReturnModal    = false;
    public ?int   $returnAssignmentId = null;
    public string $returnCondition    = 'good';
    public string $returnDate         = '';
    public string $returnNotes        = '';

    protected function jerseyRules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:home,away,training,keeper,other',
            'season'             => 'nullable|string|max:20',
            'color'              => 'nullable|string|max:50',
            'size'               => 'required|string|max:20',
            'quantity_total'     => 'required|integer|min:0',
            'quantity_available' => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'unit_price'         => 'nullable|numeric|min:0',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'notes'              => 'nullable|string',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId','name','color','season','notes','unit_price','supplier_id']);
        $this->type = 'home'; $this->size = 'L';
        $this->quantity_total = $this->quantity_available = '0';
        $this->low_stock_threshold = '2';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $j = Jersey::findOrFail($id);
        $this->editingId = $id;
        $this->fill([
            'name' => $j->name, 'type' => $j->type, 'season' => $j->season ?? '',
            'color' => $j->color ?? '', 'size' => $j->size,
            'quantity_total' => (string)$j->quantity_total,
            'quantity_available' => (string)$j->quantity_available,
            'low_stock_threshold' => (string)$j->low_stock_threshold,
            'unit_price' => (string)($j->unit_price ?? ''),
            'supplier_id' => (string)($j->supplier_id ?? ''),
            'notes' => $j->notes ?? '',
        ]);
        $this->showModal = true;
    }

    public function saveJersey(): void
    {
        $data = $this->validate($this->jerseyRules());
        $data['unit_price']  = $data['unit_price']  !== '' ? $data['unit_price']  : null;
        $data['supplier_id'] = $data['supplier_id'] !== '' ? $data['supplier_id'] : null;

        if ($this->editingId) {
            Jersey::findOrFail($this->editingId)->update($data);
        } else {
            Jersey::create($data);
        }
        $this->showModal = false;
        $this->dispatch('toast', type:'success', message: $this->editingId ? 'Maillot modifié.' : 'Maillot ajouté.');
    }

    public function delete(int $id): void
    {
        Jersey::findOrFail($id)->delete();
        $this->dispatch('toast', type:'success', message:'Maillot supprimé.');
    }

    // Attribution
    public function openAssign(int $jerseyId): void
    {
        $this->assignJerseyId = $jerseyId;
        $this->assignPlayerId = '';
        $this->assignNumber   = '';
        $this->assignSeason   = date('Y') . '-' . (date('Y') + 1);
        $this->assignDate     = now()->format('Y-m-d');
        $this->showAssignModal = true;
    }

    public function saveAssignment(): void
    {
        $this->validate([
            'assignPlayerId' => 'required|exists:players,id',
            'assignNumber'   => 'nullable|string|max:10',
            'assignSeason'   => 'nullable|string|max:20',
            'assignDate'     => 'required|date',
        ]);

        $jersey = Jersey::findOrFail($this->assignJerseyId);
        if ($jersey->quantity_available < 1) {
            $this->addError('assignPlayerId', 'Stock insuffisant pour ce maillot.');
            return;
        }

        JerseyAssignment::create([
            'jersey_id'     => $this->assignJerseyId,
            'player_id'     => $this->assignPlayerId,
            'jersey_number' => $this->assignNumber ?: null,
            'season'        => $this->assignSeason ?: null,
            'assigned_at'   => $this->assignDate,
        ]);

        $jersey->decrement('quantity_available');
        $this->showAssignModal = false;
        $this->dispatch('toast', type:'success', message:'Maillot attribué.');
    }

    // Retour
    public function openReturn(int $assignmentId): void
    {
        $this->returnAssignmentId = $assignmentId;
        $this->returnCondition    = 'good';
        $this->returnDate         = now()->format('Y-m-d');
        $this->returnNotes        = '';
        $this->showReturnModal    = true;
    }

    public function saveReturn(): void
    {
        $this->validate([
            'returnCondition' => 'required|in:good,damaged,lost',
            'returnDate'      => 'required|date',
        ]);

        $assignment = JerseyAssignment::with('jersey')->findOrFail($this->returnAssignmentId);
        $assignment->update([
            'returned_at'       => $this->returnDate,
            'condition_returned'=> $this->returnCondition,
            'notes'             => $this->returnNotes ?: null,
        ]);

        // Réintégrer au stock sauf si perdu
        if ($this->returnCondition !== 'lost') {
            $assignment->jersey->increment('quantity_available');
        }

        $this->showReturnModal = false;
        $this->dispatch('toast', type:'success', message:'Retour enregistré.');
    }

    public function returnAll(string $season): void
    {
        $assignments = JerseyAssignment::whereNull('returned_at')
            ->where('season', $season)
            ->with('jersey')
            ->get();

        foreach ($assignments as $a) {
            $a->update(['returned_at' => now()->toDateString(), 'condition_returned' => 'good']);
            $a->jersey->increment('quantity_available');
        }
        $this->dispatch('toast', type:'success', message:$assignments->count().' maillots retournés.');
    }

    public function render()
    {
        $jerseys = Jersey::with('supplier')
            ->withCount('activeAssignments')
            ->when($this->filterType,   fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterSeason, fn($q) => $q->where('season', $this->filterSeason))
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('name','like','%'.$this->search.'%')
                   ->orWhere('size','like','%'.$this->search.'%')
                   ->orWhere('color','like','%'.$this->search.'%')
            ))
            ->orderBy('name')->orderBy('size')
            ->paginate(15);

        $assignments = JerseyAssignment::with(['jersey','player'])
            ->when($this->filterSeason, fn($q) => $q->where('season', $this->filterSeason))
            ->when($this->tab === 'assignments', fn($q) => $q->orderByDesc('assigned_at'))
            ->paginate(15);

        $seasons   = Jersey::selectRaw('DISTINCT season')->whereNotNull('season')->pluck('season');
        $suppliers = Supplier::orderBy('name')->get();
        $players   = Player::orderBy('last_name')->get();

        return view('livewire.club.stock.jerseys.index', compact(
            'jerseys','assignments','seasons','suppliers','players'
        ));
    }
}
