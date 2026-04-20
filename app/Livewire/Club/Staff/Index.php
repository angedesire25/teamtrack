<?php

namespace App\Livewire\Club\Staff;

use App\Models\Staff;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url] public string $search = '';

    public bool    $showModal      = false;
    public ?int    $editingId      = null;
    public string  $first_name     = '';
    public string  $last_name      = '';
    public string  $role           = '';
    public string  $contract_type  = '';
    public string  $contract_start = '';
    public string  $contract_end   = '';
    public string  $phone          = '';
    public string  $email          = '';

    public const ROLES = [
        'Entraîneur principal', 'Entraîneur adjoint', 'Préparateur physique',
        'Médecin', 'Kinésithérapeute', 'Manager', 'Secrétaire',
        'Magasinier', 'Analyste vidéo', 'Directeur sportif', 'Autre',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'first_name', 'last_name', 'role',
            'contract_type', 'contract_start', 'contract_end', 'phone', 'email']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = Staff::findOrFail($id);
        $this->editingId      = $id;
        $this->first_name     = $s->first_name;
        $this->last_name      = $s->last_name;
        $this->role           = $s->role;
        $this->contract_type  = $s->contract_type ?? '';
        $this->contract_start = $s->contract_start?->format('Y-m-d') ?? '';
        $this->contract_end   = $s->contract_end?->format('Y-m-d') ?? '';
        $this->phone          = $s->phone ?? '';
        $this->email          = $s->email ?? '';
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'role'           => 'required|string|max:100',
            'contract_type'  => 'nullable|string|max:50',
            'contract_start' => 'nullable|date',
            'contract_end'   => 'nullable|date|after_or_equal:contract_start',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
        ]);

        $data = [
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'role'           => $this->role,
            'contract_type'  => $this->contract_type ?: null,
            'contract_start' => $this->contract_start ?: null,
            'contract_end'   => $this->contract_end ?: null,
            'phone'          => $this->phone ?: null,
            'email'          => $this->email ?: null,
        ];

        if ($this->editingId) {
            Staff::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: 'Membre du staff mis à jour.', type: 'success');
        } else {
            Staff::create($data);
            $this->dispatch('toast', message: 'Membre du staff ajouté.', type: 'success');
        }

        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        Staff::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Membre supprimé.', type: 'success');
    }

    public function render()
    {
        $staff = Staff::when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('role', 'like', "%{$this->search}%");
            }))
            ->orderBy('last_name')
            ->paginate(20);

        $expiring = Staff::whereNotNull('contract_end')
            ->where('contract_end', '>=', now())
            ->where('contract_end', '<=', now()->addDays(30))
            ->count();

        return view('livewire.club.staff.index', compact('staff', 'expiring'))
            ->title('Personnel');
    }
}
