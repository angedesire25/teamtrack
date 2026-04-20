<?php

namespace App\Livewire\Club\Donations\Donors;

use App\Models\Donation;
use App\Models\Donor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    public ?int $viewingDonorId  = null;

    public function viewDonor(int $id): void
    {
        $this->viewingDonorId = $id;
    }

    public function render()
    {
        $donors = Donor::withCount('completedDonations')
            ->withSum(['donations as total_donated' => fn($q) => $q->where('status','completed')], 'amount')
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('first_name','like','%'.$this->search.'%')
                   ->orWhere('last_name','like','%'.$this->search.'%')
                   ->orWhere('email','like','%'.$this->search.'%')
            ))
            ->orderByDesc('total_donated')
            ->paginate(20);

        $viewingDonor     = null;
        $donorDonations   = collect();
        if ($this->viewingDonorId) {
            $viewingDonor   = Donor::find($this->viewingDonorId);
            $donorDonations = Donation::with('campaign')
                ->where('donor_id', $this->viewingDonorId)
                ->where('status','completed')
                ->orderByDesc('created_at')->get();
        }

        return view('livewire.club.donations.donors.index', compact(
            'donors','viewingDonor','donorDonations'
        ));
    }
}
