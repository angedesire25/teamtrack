<?php

/**
 * Composant Livewire — Tableau de bord du super administrateur.
 * Agrège les statistiques globales de la plateforme : clubs, joueurs, MRR, alertes.
 */

namespace App\Livewire\SuperAdmin;

use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.superadmin')]
class Dashboard extends Component
{
    // -----------------------------------------------------------------------
    // Propriétés calculées (mises en cache le temps du rendu)
    // -----------------------------------------------------------------------

    /** Statistiques globales : statuts des clubs, joueurs, MRR */
    #[Computed]
    public function stats(): array
    {
        // MRR = somme des prix des plans des clubs actifs (abonnements mensuels)
        $mrr = (int) Tenant::where('tenants.status', 'active')
            ->join('plans', 'tenants.plan_id', '=', 'plans.id')
            ->selectRaw('COALESCE(SUM(plans.price), 0) as total')
            ->value('total');

        return [
            'actifs'    => Tenant::where('status', 'active')->count(),
            'suspendus' => Tenant::where('status', 'suspended')->count(),
            'trial'     => Tenant::where('status', 'trial')->count(),
            'joueurs'   => Player::count(),
            'mrr'       => $mrr,
        ];
    }

    /** Clubs en période d'essai expirant dans moins de 7 jours */
    #[Computed]
    public function trialExpiringSoon(): Collection
    {
        return Tenant::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->with('plan')
            ->orderBy('trial_ends_at')
            ->get();
    }

    /** 8 derniers clubs inscrits sur la plateforme */
    #[Computed]
    public function recentTenants(): Collection
    {
        return Tenant::with('plan')
            ->latest()
            ->take(8)
            ->get();
    }

    /** Données pour le graphique d'évolution des inscriptions (6 derniers mois) */
    #[Computed]
    public function graphData(): array
    {
        $mois = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        return [
            'labels' => $mois->map(
                fn ($m) => ucfirst($m->locale('fr')->isoFormat('MMM YYYY'))
            )->toArray(),
            'data' => $mois->map(
                fn ($m) => Tenant::whereYear('created_at', $m->year)
                    ->whereMonth('created_at', $m->month)
                    ->count()
            )->toArray(),
        ];
    }

    public function render(): View
    {
        return view('livewire.super-admin.dashboard', [
            'stats'             => $this->stats,
            'trialExpiringSoon' => $this->trialExpiringSoon,
            'recentTenants'     => $this->recentTenants,
            'graphData'         => $this->graphData,
        ]);
    }
}
