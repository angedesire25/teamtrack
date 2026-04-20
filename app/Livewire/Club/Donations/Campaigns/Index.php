<?php

namespace App\Livewire\Club\Donations\Campaigns;

use App\Models\DonationCampaign;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Index extends Component
{
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $title            = '';
    public string $description      = '';
    public string $goal_amount      = '';
    public string $suggested_amounts= '5000,10000,25000,50000';
    public string $start_date       = '';
    public string $end_date         = '';
    public bool   $is_active        = true;
    public bool   $allow_recurring  = true;
    public bool   $allow_anonymous  = true;

    protected function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'goal_amount'       => 'nullable|numeric|min:1',
            'suggested_amounts' => 'nullable|string',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'is_active'         => 'boolean',
            'allow_recurring'   => 'boolean',
            'allow_anonymous'   => 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId','title','description','goal_amount','start_date','end_date']);
        $this->suggested_amounts = '5000,10000,25000,50000';
        $this->is_active = $this->allow_recurring = $this->allow_anonymous = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $c = DonationCampaign::findOrFail($id);
        $this->editingId = $id;
        $this->fill([
            'title'             => $c->title,
            'description'       => $c->description ?? '',
            'goal_amount'       => (string)($c->goal_amount ?? ''),
            'suggested_amounts' => implode(',', $c->suggested_amounts ?? [5000,10000,25000,50000]),
            'start_date'        => $c->start_date?->format('Y-m-d') ?? '',
            'end_date'          => $c->end_date?->format('Y-m-d') ?? '',
            'is_active'         => $c->is_active,
            'allow_recurring'   => $c->allow_recurring,
            'allow_anonymous'   => $c->allow_anonymous,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['goal_amount']      = $data['goal_amount'] !== '' ? $data['goal_amount'] : null;
        $data['suggested_amounts'] = array_map(
            fn($v) => (int) trim($v),
            array_filter(explode(',', $this->suggested_amounts))
        );
        $data['start_date'] = $data['start_date'] ?: null;
        $data['end_date']   = $data['end_date']   ?: null;

        if ($this->editingId) {
            DonationCampaign::findOrFail($this->editingId)->update($data);
        } else {
            DonationCampaign::create($data);
        }
        $this->showModal = false;
        $this->dispatch('toast', type:'success', message: $this->editingId ? 'Campagne modifiée.' : 'Campagne créée.');
    }

    public function toggleActive(int $id): void
    {
        $c = DonationCampaign::findOrFail($id);
        $c->update(['is_active' => !$c->is_active]);
    }

    public function delete(int $id): void
    {
        DonationCampaign::findOrFail($id)->delete();
        $this->dispatch('toast', type:'success', message:'Campagne supprimée.');
    }

    public function render()
    {
        return view('livewire.club.donations.campaigns.index', [
            'campaigns' => DonationCampaign::withCount('completedDonations')
                ->withSum(['donations as collected' => fn($q) => $q->where('status','completed')], 'amount')
                ->orderByDesc('created_at')->get(),
        ]);
    }
}
