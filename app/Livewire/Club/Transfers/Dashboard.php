<?php

namespace App\Livewire\Club\Transfers;

use App\Models\Transfer;
use App\Models\TransferNegotiation;
use App\Models\TransferWindow;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Dashboard extends Component
{
    public function render()
    {
        $tenantId = app('tenant')->id;

        $currentWindow = TransferWindow::currentWindow($tenantId);

        $stats = [
            'outgoing_active'   => Transfer::where('tenant_id', $tenantId)->where('direction', 'outgoing')->whereNotIn('status', ['finalized', 'cancelled'])->count(),
            'incoming_active'   => Transfer::where('tenant_id', $tenantId)->where('direction', 'incoming')->whereNotIn('status', ['finalized', 'cancelled'])->count(),
            'finalized_year'    => Transfer::where('tenant_id', $tenantId)->where('status', 'finalized')->whereYear('finalized_at', now()->year)->count(),
            'total_fees'        => Transfer::where('tenant_id', $tenantId)->where('status', 'finalized')->sum('agreed_fee'),
        ];

        $recentNegotiations = TransferNegotiation::whereHas('transfer', fn ($q) => $q->where('tenant_id', $tenantId))
            ->with(['transfer.player'])
            ->orderByDesc('date')
            ->limit(8)
            ->get();

        $activeOutgoing = Transfer::where('tenant_id', $tenantId)
            ->where('direction', 'outgoing')
            ->whereNotIn('status', ['finalized', 'cancelled'])
            ->with('player')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $activeIncoming = Transfer::where('tenant_id', $tenantId)
            ->where('direction', 'incoming')
            ->whereNotIn('status', ['finalized', 'cancelled'])
            ->with('player')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('livewire.club.transfers.dashboard', compact(
            'currentWindow', 'stats', 'recentNegotiations', 'activeOutgoing', 'activeIncoming'
        ));
    }
}
