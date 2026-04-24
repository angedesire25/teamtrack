<?php

namespace App\Livewire\Club\Transfers;

use App\Models\Transfer;
use App\Models\TransferNegotiation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Show extends Component
{
    public Transfer $transfer;

    // Modale d'édition
    public bool $showEditModal = false;
    public string $counterpart_club    = '';
    public string $counterpart_contact = '';
    public string $asking_price        = '';
    public string $agreed_fee          = '';
    public string $loan_duration_months= '';
    public string $loan_start_date     = '';
    public string $loan_end_date       = '';
    public string $notes               = '';
    public array  $clauses             = [];

    // Modale de négociation
    public bool   $showNegModal = false;
    public string $negDate      = '';
    public string $negNote      = '';
    public string $negAmount    = '';
    public string $negStatus    = '';

    public function mount(int $transfer): void
    {
        $this->transfer = Transfer::with(['player', 'negotiations'])->findOrFail($transfer);
        abort_unless($this->transfer->tenant_id === app('tenant')->id, 403);
    }

    public function openEdit(): void
    {
        $t = $this->transfer;
        $this->counterpart_club     = $t->counterpart_club ?? '';
        $this->counterpart_contact  = $t->counterpart_contact ?? '';
        $this->asking_price         = $t->asking_price ?? '';
        $this->agreed_fee           = $t->agreed_fee ?? '';
        $this->loan_duration_months = $t->loan_duration_months ?? '';
        $this->loan_start_date      = $t->loan_start_date?->format('Y-m-d') ?? '';
        $this->loan_end_date        = $t->loan_end_date?->format('Y-m-d') ?? '';
        $this->notes                = $t->notes ?? '';
        $this->clauses              = $t->clauses ?? [];
        $this->showEditModal        = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'counterpart_club'     => 'nullable|string|max:150',
            'counterpart_contact'  => 'nullable|string|max:150',
            'asking_price'         => 'nullable|integer|min:0',
            'agreed_fee'           => 'nullable|integer|min:0',
            'loan_duration_months' => 'nullable|integer|min:1|max:60',
            'loan_start_date'      => 'nullable|date',
            'loan_end_date'        => 'nullable|date',
            'notes'                => 'nullable|string',
        ]);

        $this->transfer->update([
            'counterpart_club'     => $this->counterpart_club ?: null,
            'counterpart_contact'  => $this->counterpart_contact ?: null,
            'asking_price'         => $this->asking_price ?: null,
            'agreed_fee'           => $this->agreed_fee ?: null,
            'loan_duration_months' => $this->loan_duration_months ?: null,
            'loan_start_date'      => $this->loan_start_date ?: null,
            'loan_end_date'        => $this->loan_end_date ?: null,
            'notes'                => $this->notes ?: null,
            'clauses'              => $this->clauses ?: null,
        ]);

        $this->transfer->refresh();
        $this->showEditModal = false;
    }

    public function toggleClause(string $key): void
    {
        $clauses = $this->clauses;
        $clauses[$key] = !($clauses[$key] ?? false);
        $this->clauses = $clauses;
    }

    public function advanceStatus(string $newStatus): void
    {
        abort_unless(in_array($newStatus, $this->transfer->nextStatuses()), 403);

        $data = ['status' => $newStatus];
        if ($newStatus === 'finalized') {
            $data['finalized_at'] = now();
        }

        $this->transfer->update($data);
        $this->transfer->refresh();
    }

    public function openNeg(): void
    {
        $this->negDate   = today()->format('Y-m-d');
        $this->negNote   = '';
        $this->negAmount = '';
        $this->negStatus = $this->transfer->status;
        $this->showNegModal = true;
    }

    public function saveNeg(): void
    {
        $this->validate([
            'negDate'   => 'required|date',
            'negNote'   => 'required|string|min:5',
            'negAmount' => 'nullable|integer|min:0',
            'negStatus' => 'nullable|in:listed,negotiating,offer_received,agreed,finalized,cancelled',
        ]);

        TransferNegotiation::create([
            'transfer_id'    => $this->transfer->id,
            'date'           => $this->negDate,
            'note'           => $this->negNote,
            'amount_proposed'=> $this->negAmount ?: null,
            'status_after'   => $this->negStatus ?: null,
        ]);

        if ($this->negStatus && $this->negStatus !== $this->transfer->status) {
            $data = ['status' => $this->negStatus];
            if ($this->negStatus === 'finalized') {
                $data['finalized_at'] = now();
            }
            $this->transfer->update($data);
        }

        $this->transfer->load('negotiations');
        $this->transfer->refresh();
        $this->showNegModal = false;
    }

    public function deleteNeg(int $id): void
    {
        TransferNegotiation::findOrFail($id)->delete();
        $this->transfer->load('negotiations');
    }

    public function render()
    {
        return view('livewire.club.transfers.show');
    }
}
