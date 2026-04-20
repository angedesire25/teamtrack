<?php

namespace App\Livewire\SuperAdmin\Clubs;

use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Détail club')]
class Show extends Component
{
    public Tenant $tenant;

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant->load(['plan', 'users', 'payments.tenant']);
    }

    // -----------------------------------------------------------------------
    // Actions rapides
    // -----------------------------------------------------------------------

    public function activate(): void
    {
        $this->tenant->update(['status' => 'active', 'suspended_at' => null]);
        $this->tenant->refresh();
    }

    public function suspend(): void
    {
        $this->tenant->update(['status' => 'suspended', 'suspended_at' => now()]);
        $this->tenant->refresh();
    }

    // -----------------------------------------------------------------------
    // Données calculées
    // -----------------------------------------------------------------------

    /** Statistiques internes du club */
    #[Computed]
    public function stats(): array
    {
        return [
            'joueurs'      => $this->tenant->players()->count(),
            'categories'   => $this->tenant->categories()->count(),
            'equipes'       => $this->tenant->teams()->count(),
            'utilisateurs' => $this->tenant->users()->count(),
        ];
    }

    /** 10 derniers paiements du club */
    #[Computed]
    public function payments(): Collection
    {
        return $this->tenant->payments()->latest()->take(10)->get();
    }

    /** Utilisateurs du club */
    #[Computed]
    public function users(): Collection
    {
        return $this->tenant->users()->orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.super-admin.clubs.show', [
            'tenant'   => $this->tenant,
            'stats'    => $this->stats,
            'payments' => $this->payments,
            'users'    => $this->users,
        ]);
    }
}
