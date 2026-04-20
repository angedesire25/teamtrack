<?php

namespace App\Livewire\Club;

use App\Models\Category;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Dashboard extends Component
{
    public function render()
    {
        $tenant = app()->has('tenant') ? app('tenant') : auth()->user()->tenant;

        // --- Statistiques clés ---
        $stats = [
            'players'      => Player::where('status', 'active')->count(),
            'categories'   => Category::count(),
            'teams'        => Team::count(),
            'staff'        => Staff::count(),
        ];

        // --- Joueurs avec statut anormal ---
        $alertPlayers = Player::with(['team', 'category'])
            ->whereIn('status', ['injured', 'suspended'])
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                  ->whereNotNull('license_expires_at')
                  ->where('license_expires_at', '<=', Carbon::now()->addDays(30));
            })
            ->orderBy('status')
            ->limit(8)
            ->get();

        // --- Membres du staff avec contrat expirant bientôt ---
        $expiringContracts = Staff::whereNotNull('contract_end')
            ->where('contract_end', '>=', Carbon::today())
            ->where('contract_end', '<=', Carbon::today()->addDays(30))
            ->orderBy('contract_end')
            ->limit(5)
            ->get();

        // --- Joueurs récemment ajoutés ---
        $recentPlayers = Player::with(['team', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.club.dashboard', compact(
            'tenant', 'stats', 'alertPlayers', 'expiringContracts', 'recentPlayers'
        ))->title($tenant->name . ' — Tableau de bord');
    }
}
