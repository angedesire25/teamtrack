<?php

namespace App\Livewire\Club\Finance;

use App\Models\Donation;
use App\Models\Expense;
use App\Models\PlayerSubscription;
use App\Models\SubscriptionPayment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.club')]
class Dashboard extends Component
{
    #[Url]
    public int $year;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function render()
    {
        $tenantId = app('tenant')->id;
        $season   = \App\Models\SubscriptionPlan::currentSeason();

        // Cotisations de la saison en cours
        $subscriptions = PlayerSubscription::where('tenant_id', $tenantId)->where('season', $season)->get();
        $totalDue      = $subscriptions->sum('amount_due');
        $totalCollected = $subscriptions->sum('amount_paid');
        $overdueCount  = $subscriptions->where('status', 'overdue')->count();
        $recoveryRate  = $totalDue > 0 ? round($totalCollected / $totalDue * 100) : 0;

        // Dépenses annuelles
        $totalExpenses = Expense::where('tenant_id', $tenantId)->whereYear('date', $this->year)->sum('amount');

        // Recettes annuelles (cotisations + dons)
        $incomeSubscriptions = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereYear('payment_date', $this->year)->sum('amount');
        $incomeDonations = Donation::where('tenant_id', $tenantId)->where('status', 'completed')
            ->whereYear('created_at', $this->year)->sum('amount');
        $totalIncome = $incomeSubscriptions + $incomeDonations;
        $solde = $totalIncome - $totalExpenses;

        // Données du graphique mensuel (12 mois de l'année sélectionnée)
        $monthlyIncome   = [];
        $monthlyExpenses = [];
        $monthLabels     = [];

        for ($m = 1; $m <= 12; $m++) {
            $subPay = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenantId))
                ->whereYear('payment_date', $this->year)->whereMonth('payment_date', $m)->sum('amount');
            $don = Donation::where('tenant_id', $tenantId)->where('status', 'completed')
                ->whereYear('created_at', $this->year)->whereMonth('created_at', $m)->sum('amount');
            $exp = Expense::where('tenant_id', $tenantId)->whereYear('date', $this->year)->whereMonth('date', $m)->sum('amount');
            $monthlyIncome[]   = $subPay + $don;
            $monthlyExpenses[] = $exp;
            $monthLabels[]     = mb_strtoupper(\Carbon\Carbon::create($this->year, $m)->translatedFormat('M'));
        }

        // Dépenses par catégorie
        $byCategory = Expense::where('tenant_id', $tenantId)
            ->whereYear('date', $this->year)
            ->with('category')
            ->get()
            ->groupBy(fn ($e) => $e->category?->name ?? 'Autres')
            ->map(fn ($g) => $g->sum('amount'))
            ->sortByDesc(fn ($v) => $v)
            ->take(6);

        // Paiements récents
        $recentPayments = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('subscription.player')
            ->orderByDesc('payment_date')
            ->limit(8)
            ->get();

        return view('livewire.club.finance.dashboard', compact(
            'season', 'totalDue', 'totalCollected', 'overdueCount', 'recoveryRate',
            'totalExpenses', 'totalIncome', 'solde',
            'monthlyIncome', 'monthlyExpenses', 'monthLabels',
            'byCategory', 'recentPayments'
        ));
    }
}
