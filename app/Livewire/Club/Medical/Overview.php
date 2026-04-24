<?php

namespace App\Livewire\Club\Medical;

use App\Models\Injury;
use App\Models\MedicalCertificate;
use App\Models\MedicalClearance;
use App\Models\Player;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Overview extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $clearanceFilter = '';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingClearanceFilter(): void { $this->resetPage(); }

    public function render()
    {
        abort_unless(
            auth()->user()->hasAnyRole(['admin_club', 'staff_medical']),
            403
        );

        $tenantId = app('tenant')->id;

        // Indicateurs clés
        $activeInjuries = Injury::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'recovering'])
            ->count();

        $expiredCerts = MedicalCertificate::where('tenant_id', $tenantId)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now())
            ->count();

        $expiringSoonCerts = MedicalCertificate::where('tenant_id', $tenantId)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now())
            ->whereDate('expires_at', '<=', now()->addDays(30))
            ->count();

        // Dernière aptitude par joueur via sous-requête
        $latestClearanceIds = MedicalClearance::where('tenant_id', $tenantId)
            ->selectRaw('MAX(id) as id')
            ->groupBy('player_id')
            ->pluck('id');

        $clearanceCounts = MedicalClearance::whereIn('id', $latestClearanceIds)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Liste des joueurs
        $players = Player::where('players.tenant_id', $tenantId)
            ->where('status', 'active')
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('first_name', 'like', '%'.$this->search.'%')
                       ->orWhere('last_name',  'like', '%'.$this->search.'%')
                )
            )
            ->with(['latestClearance', 'injuries' => fn($q) => $q->whereIn('status', ['active', 'recovering'])])
            ->when($this->clearanceFilter, function ($q) use ($latestClearanceIds) {
                $filter = $this->clearanceFilter;
                if ($filter === 'none') {
                    $q->whereDoesntHave('medicalClearances');
                } else {
                    $q->whereHas('latestClearance', fn($q2) => $q2->where('status', $filter));
                }
            })
            ->orderBy('last_name')
            ->paginate(20);

        return view('livewire.club.medical.overview', [
            'players'            => $players,
            'activeInjuries'     => $activeInjuries,
            'expiredCerts'       => $expiredCerts,
            'expiringSoonCerts'  => $expiringSoonCerts,
            'clearanceCounts'    => $clearanceCounts,
        ])->title('Suivi Médical');
    }
}
