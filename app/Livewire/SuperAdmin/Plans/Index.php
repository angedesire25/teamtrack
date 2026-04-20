<?php

namespace App\Livewire\SuperAdmin\Plans;

use App\Models\Plan;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Gestion des plans')]
class Index extends Component
{
    // -----------------------------------------------------------------------
    // Actions
    // -----------------------------------------------------------------------

    /** Active ou désactive un plan */
    public function toggleActive(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_active' => ! $plan->is_active]);
    }

    /** Supprime un plan (impossible s'il a des clubs abonnés) */
    public function delete(int $id): void
    {
        $plan = Plan::withCount('tenants')->findOrFail($id);

        if ($plan->tenants_count > 0) {
            session()->flash('error', "Impossible de supprimer ce plan : {$plan->tenants_count} club(s) l'utilisent encore.");
            return;
        }

        $plan->delete();
        session()->flash('success', 'Plan supprimé.');
    }

    // -----------------------------------------------------------------------
    // Données
    // -----------------------------------------------------------------------

    /** Plans avec nombre de clubs abonnés */
    #[Computed]
    public function plans(): Collection
    {
        return Plan::withCount('tenants')->orderBy('price')->get();
    }

    public function render(): View
    {
        return view('livewire.super-admin.plans.index', [
            'plans' => $this->plans,
        ]);
    }
}
