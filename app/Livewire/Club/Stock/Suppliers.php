<?php

namespace App\Livewire\Club\Stock;

use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Suppliers extends Component
{
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $name         = '';
    public string $contact_name = '';
    public string $email        = '';
    public string $phone        = '';
    public string $address      = '';
    public string $notes        = '';
    public bool   $is_active    = true;

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId','name','contact_name','email','phone','address','notes']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = Supplier::findOrFail($id);
        $this->editingId = $id;
        $this->fill([
            'name'         => $s->name,
            'contact_name' => $s->contact_name ?? '',
            'email'        => $s->email ?? '',
            'phone'        => $s->phone ?? '',
            'address'      => $s->address ?? '',
            'notes'        => $s->notes ?? '',
            'is_active'    => $s->is_active,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
        } else {
            Supplier::create($data);
        }
        $this->showModal = false;
        $this->dispatch('toast', type:'success', message: $this->editingId ? 'Fournisseur modifié.' : 'Fournisseur ajouté.');
    }

    public function delete(int $id): void
    {
        Supplier::findOrFail($id)->delete();
        $this->dispatch('toast', type:'success', message:'Fournisseur supprimé.');
    }

    public function render()
    {
        return view('livewire.club.stock.suppliers', [
            'suppliers' => Supplier::withCount(['jerseys','equipmentItems'])->orderBy('name')->get(),
        ]);
    }
}
