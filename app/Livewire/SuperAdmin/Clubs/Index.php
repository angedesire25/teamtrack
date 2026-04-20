<?php

namespace App\Livewire\SuperAdmin\Clubs;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
#[Title('Gestion des clubs')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterPlan = '';

    #[Url]
    public string $filterCountry = '';

    /** Réinitialise la pagination lors d'un changement de filtre */
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterPlan(): void { $this->resetPage(); }
    public function updatingFilterCountry(): void { $this->resetPage(); }

    // -----------------------------------------------------------------------
    // Actions rapides
    // -----------------------------------------------------------------------

    /** Active un club suspendu ou en trial */
    public function activate(int $id): void
    {
        Tenant::findOrFail($id)->update(['status' => 'active', 'suspended_at' => null]);
        $this->dispatch('notify', type: 'success', message: 'Club activé avec succès.');
    }

    /** Suspend un club actif */
    public function suspend(int $id): void
    {
        Tenant::findOrFail($id)->update(['status' => 'suspended', 'suspended_at' => now()]);
        $this->dispatch('notify', type: 'warning', message: 'Club suspendu.');
    }

    /** Soft-delete un club */
    public function delete(int $id): void
    {
        Tenant::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'error', message: 'Club supprimé (soft delete).');
    }

    // -----------------------------------------------------------------------
    // Données
    // -----------------------------------------------------------------------

    /** Liste paginée des clubs avec filtres appliqués */
    #[Computed]
    public function tenants(): LengthAwarePaginator
    {
        return Tenant::with('plan')
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('subdomain', 'like', "%{$this->search}%")
            )
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPlan, fn ($q) => $q->where('plan_id', $this->filterPlan))
            ->when($this->filterCountry, fn ($q) => $q->where('country', $this->filterCountry))
            ->latest()
            ->paginate(15);
    }

    /** Plans disponibles pour le filtre */
    #[Computed]
    public function plans(): Collection
    {
        return Plan::orderBy('name')->get();
    }

    /** Pays distincts pour le filtre */
    #[Computed]
    public function countries(): Collection
    {
        return Tenant::distinct()->orderBy('country')->pluck('country');
    }

    public function render(): View
    {
        return view('livewire.super-admin.clubs.index', [
            'tenants'   => $this->tenants,
            'plans'     => $this->plans,
            'countries' => $this->countries,
        ]);
    }
}
