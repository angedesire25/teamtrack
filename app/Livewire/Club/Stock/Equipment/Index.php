<?php

namespace App\Livewire\Club\Stock\Equipment;

use App\Models\EquipmentItem;
use App\Models\EquipmentMovement;
use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url] public string $filterCategory  = '';
    #[Url] public string $filterCondition = '';
    #[Url] public string $search          = '';
    #[Url] public string $tab             = 'items'; // items | movements — onglet actif

    // Modal article
    public bool   $showModal = false;
    public ?int   $editingId = null;
    public string $name      = '';
    public string $category  = '';
    public string $condition = 'good';
    public string $quantity_total      = '0';
    public string $quantity_available  = '0';
    public string $low_stock_threshold = '2';
    public string $unit_price   = '';
    public string $reference    = '';
    public string $supplier_id  = '';
    public string $notes        = '';

    // Modal mouvement
    public bool   $showMovementModal  = false;
    public ?int   $movementItemId     = null;
    public string $movementType       = 'out';
    public string $movementQty        = '1';
    public string $movementReason     = '';
    public string $movementExpReturn  = '';
    public string $movementNotes      = '';

    // Modal retour matériel
    public bool   $showReturnModal  = false;
    public ?int   $returnMovementId = null;
    public string $returnDate       = '';

    protected function itemRules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'category'           => 'required|string|max:100',
            'condition'          => 'required|in:new,good,repair,out_of_service',
            'quantity_total'     => 'required|integer|min:0',
            'quantity_available' => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'unit_price'         => 'nullable|numeric|min:0',
            'reference'          => 'nullable|string|max:100',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'notes'              => 'nullable|string',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId','name','reference','notes','unit_price','supplier_id']);
        $this->category  = ''; $this->condition = 'good';
        $this->quantity_total = $this->quantity_available = '0';
        $this->low_stock_threshold = '2';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = EquipmentItem::findOrFail($id);
        $this->editingId = $id;
        $this->fill([
            'name'               => $item->name,
            'category'           => $item->category,
            'condition'          => $item->condition,
            'quantity_total'     => (string)$item->quantity_total,
            'quantity_available' => (string)$item->quantity_available,
            'low_stock_threshold'=> (string)$item->low_stock_threshold,
            'unit_price'         => (string)($item->unit_price ?? ''),
            'reference'          => $item->reference ?? '',
            'supplier_id'        => (string)($item->supplier_id ?? ''),
            'notes'              => $item->notes ?? '',
        ]);
        $this->showModal = true;
    }

    public function saveItem(): void
    {
        $data = $this->validate($this->itemRules());
        $data['unit_price']  = $data['unit_price']  !== '' ? $data['unit_price']  : null;
        $data['supplier_id'] = $data['supplier_id'] !== '' ? $data['supplier_id'] : null;

        if ($this->editingId) {
            EquipmentItem::findOrFail($this->editingId)->update($data);
        } else {
            EquipmentItem::create($data);
        }
        $this->showModal = false;
        $this->dispatch('toast', type:'success', message: $this->editingId ? 'Article modifié.' : 'Article ajouté.');
    }

    public function delete(int $id): void
    {
        EquipmentItem::findOrFail($id)->delete();
        $this->dispatch('toast', type:'success', message:'Article supprimé.');
    }

    // Mouvement entrée/sortie
    public function openMovement(int $itemId, string $type = 'out'): void
    {
        $this->movementItemId    = $itemId;
        $this->movementType      = $type;
        $this->movementQty       = '1';
        $this->movementReason    = '';
        $this->movementExpReturn = '';
        $this->movementNotes     = '';
        $this->showMovementModal = true;
    }

    public function saveMovement(): void
    {
        $this->validate([
            'movementType'      => 'required|in:in,out,return,adjustment',
            'movementQty'       => 'required|integer|min:1',
            'movementReason'    => 'nullable|string|max:255',
            'movementExpReturn' => 'nullable|date',
        ]);

        $item = EquipmentItem::findOrFail($this->movementItemId);
        $qty  = (int) $this->movementQty;

        if (in_array($this->movementType, ['out']) && $item->quantity_available < $qty) {
            $this->addError('movementQty', 'Stock disponible insuffisant ('.$item->quantity_available.').');
            return;
        }

        EquipmentMovement::create([
            'equipment_item_id'  => $item->id,
            'user_id'            => auth()->id(),
            'type'               => $this->movementType,
            'quantity'           => $qty,
            'reason'             => $this->movementReason ?: null,
            'expected_return_at' => $this->movementExpReturn ?: null,
            'notes'              => $this->movementNotes ?: null,
        ]);

        // Mise à jour du stock disponible
        match($this->movementType) {
            'in', 'return' => $item->increment('quantity_available', $qty),
            'out'          => $item->decrement('quantity_available', $qty),
            'adjustment'   => $item->update(['quantity_available' => $qty, 'quantity_total' => $qty]),
            default        => null,
        };

        $this->showMovementModal = false;
        $this->dispatch('toast', type:'success', message:'Mouvement enregistré.');
    }

    public function markReturned(int $movementId): void
    {
        $movement = EquipmentMovement::with('item')->findOrFail($movementId);
        $movement->update(['returned_at' => now()->toDateString()]);
        $movement->item->increment('quantity_available', $movement->quantity);
        $this->dispatch('toast', type:'success', message:'Retour enregistré.');
    }

    public function render()
    {
        $items = EquipmentItem::with('supplier')
            ->withCount('movements')
            ->when($this->filterCategory,  fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterCondition, fn($q) => $q->where('condition', $this->filterCondition))
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('name','like','%'.$this->search.'%')
                   ->orWhere('reference','like','%'.$this->search.'%')
            ))
            ->orderBy('category')->orderBy('name')
            ->paginate(15);

        $movements = EquipmentMovement::with(['item','user'])
            ->when($this->tab === 'movements', fn($q) => $q->orderByDesc('created_at'))
            ->paginate(15);

        $suppliers  = Supplier::orderBy('name')->get();
        $categories = EquipmentItem::categories();

        return view('livewire.club.stock.equipment.index', compact(
            'items','movements','suppliers','categories'
        ));
    }
}
