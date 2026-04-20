<?php

namespace App\Livewire\Club\Donations;

use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Donor;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Dashboard extends Component
{
    public function render()
    {
        $totalCollected  = Donation::where('status','completed')->sum('amount');
        $totalDonors     = Donor::count();
        $totalDonations  = Donation::where('status','completed')->count();
        $recurringActive = Donation::where('status','completed')
            ->whereIn('frequency',['monthly','annual'])
            ->whereNotNull('stripe_subscription_id')
            ->count();

        // Évolution mensuelle (12 derniers mois)
        $monthly = Donation::where('status','completed')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthLabels  = [];
        $monthAmounts = [];
        $monthCounts  = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $monthLabels[]  = now()->subMonths($i)->translatedFormat('M Y');
            $monthAmounts[] = (float) ($monthly[$key]->total ?? 0);
            $monthCounts[]  = (int)   ($monthly[$key]->count ?? 0);
        }

        $campaigns       = DonationCampaign::withCount(['completedDonations'])->orderByDesc('created_at')->get();
        $recentDonations = Donation::with(['donor','campaign'])
            ->where('status','completed')
            ->orderByDesc('created_at')
            ->limit(10)->get();

        return view('livewire.club.donations.dashboard', compact(
            'totalCollected','totalDonors','totalDonations','recurringActive',
            'monthLabels','monthAmounts','monthCounts',
            'campaigns','recentDonations'
        ));
    }
}
