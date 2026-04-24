<?php

namespace App\Livewire\Club\Transfers;

use App\Models\TransferWindow;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Windows extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name         = '';
    public string $type         = 'summer';
    public string $start_date   = '';
    public string $end_date     = '';
    public bool   $is_active    = true;

    protected function rules(): array
    {
        return [
            'name'       => 'required|string|max:100',
            'type'       => 'required|in:summer,winter,custom',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'start_date', 'end_date']);
        $this->type      = 'summer';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $w = TransferWindow::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $w->name;
        $this->type        = $w->type;
        $this->start_date  = $w->start_date->format('Y-m-d');
        $this->end_date    = $w->end_date->format('Y-m-d');
        $this->is_active   = $w->is_active;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate();
        $tenantId = app('tenant')->id;

        $data = [
            'tenant_id'  => $tenantId,
            'name'       => $this->name,
            'type'       => $this->type,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'is_active'  => $this->is_active,
        ];

        if ($this->editingId) {
            TransferWindow::findOrFail($this->editingId)->update($data);
        } else {
            TransferWindow::create($data);
        }

        $this->showModal = false;
        $this->dispatch('$refresh');
    }

    public function toggleActive(int $id): void
    {
        $w = TransferWindow::findOrFail($id);
        $w->update(['is_active' => !$w->is_active]);
    }

    public function delete(int $id): void
    {
        TransferWindow::findOrFail($id)->delete();
    }

    public function render()
    {
        $windows = TransferWindow::where('tenant_id', app('tenant')->id)
            ->orderByDesc('start_date')
            ->get();

        return view('livewire.club.transfers.windows', compact('windows'));
    }
}
