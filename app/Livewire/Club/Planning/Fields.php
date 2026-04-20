<?php

namespace App\Livewire\Club\Planning;

use App\Models\Field;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Fields extends Component
{
    public bool   $showModal  = false;
    public ?int   $editingId  = null;

    public string $name     = '';
    public string $address  = '';
    public string $surface  = '';
    public string $capacity = '';
    public string $notes    = '';
    public bool   $is_active = true;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string|max:255',
            'surface'  => 'nullable|string|max:100',
            'capacity' => 'nullable|integer|min:0',
            'notes'    => 'nullable|string',
            'is_active'=> 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId','name','address','surface','capacity','notes']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $field = Field::findOrFail($id);
        $this->editingId = $id;
        $this->fill([
            'name'      => $field->name,
            'address'   => $field->address ?? '',
            'surface'   => $field->surface ?? '',
            'capacity'  => (string) ($field->capacity ?? ''),
            'notes'     => $field->notes ?? '',
            'is_active' => $field->is_active,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['capacity'] = $data['capacity'] !== '' ? (int)$data['capacity'] : null;

        if ($this->editingId) {
            Field::findOrFail($this->editingId)->update($data);
        } else {
            Field::create($data);
        }

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: $this->editingId ? 'Terrain modifié.' : 'Terrain ajouté.');
    }

    public function toggleActive(int $id): void
    {
        $field = Field::findOrFail($id);
        $field->update(['is_active' => !$field->is_active]);
    }

    public function delete(int $id): void
    {
        Field::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Terrain supprimé.');
    }

    public function render()
    {
        return view('livewire.club.planning.fields', [
            'fields' => Field::orderBy('name')->get(),
        ]);
    }
}
