<?php

namespace App\Livewire\Club\Players;

use App\Models\Category;
use App\Models\Player;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url] public string $search       = '';
    #[Url] public string $filterStatus = '';
    #[Url] public string $filterCat    = '';
    #[Url] public string $filterTeam   = '';
    #[Url] public string $filterPos    = '';

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterCat(): void    { $this->resetPage(); }
    public function updatingFilterTeam(): void   { $this->resetPage(); }

    /** Changement rapide du statut d'un joueur */
    public function changeStatus(int $id, string $status): void
    {
        Player::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('toast', message: 'Statut mis à jour.', type: 'success');
    }

    /** Suppression (soft delete) */
    public function delete(int $id): void
    {
        Player::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Joueur supprimé.', type: 'success');
    }

    /** Export CSV de la liste filtrée */
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $players = $this->buildQuery()->get();

        return response()->streamDownload(function () use ($players) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($out, ['Nom', 'Prénom', 'Catégorie', 'Équipe', 'Poste', 'Statut', 'Licence', 'Expiration licence'], ';');
            foreach ($players as $p) {
                fputcsv($out, [
                    $p->last_name, $p->first_name,
                    $p->category?->name, $p->team?->name,
                    $p->position, $p->statusLabel(),
                    $p->license_number, $p->license_expires_at?->format('d/m/Y'),
                ], ';');
            }
            fclose($out);
        }, 'joueurs_' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function buildQuery()
    {
        return Player::with(['category', 'team'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('jersey_number', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCat,    fn($q) => $q->where('category_id', $this->filterCat))
            ->when($this->filterTeam,   fn($q) => $q->where('team_id', $this->filterTeam))
            ->when($this->filterPos,    fn($q) => $q->where('position', $this->filterPos))
            ->orderBy('last_name');
    }

    public function render()
    {
        return view('livewire.club.players.index', [
            'players'    => $this->buildQuery()->paginate(20),
            'categories' => Category::orderBy('name')->get(),
            'teams'      => Team::orderBy('name')->get(),
            'positions'  => ['Gardien', 'Défenseur', 'Milieu', 'Attaquant'],
            'statuses'   => [
                'active' => 'Actif', 'injured' => 'Blessé', 'suspended' => 'Suspendu',
                'loaned' => 'Prêté', 'transferred' => 'Transféré', 'former' => 'Ancien',
            ],
        ])->title('Joueurs');
    }
}
