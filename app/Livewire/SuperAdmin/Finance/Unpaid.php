<?php

namespace App\Livewire\SuperAdmin\Finance;

use App\Mail\PaymentReminderMail;
use App\Models\PaymentReminder;
use App\Models\Tenant;
use App\Models\TenantActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.superadmin', ['pageTitle' => 'Impayés'])]
class Unpaid extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $urgencyFilter = 'all'; // all | critical | warning | ok

    // ── Modal relance ───────────────────────────────────────────────────────
    public bool    $showReminderModal  = false;
    public ?int    $selectedTenantId   = null;
    public string  $selectedTenantName = '';
    public int     $selectedAmountDue  = 0;
    public string  $reminderNote       = '';

    // ── Modal historique ────────────────────────────────────────────────────
    public bool   $showHistoryModal = false;
    public string $historyTenantName = '';

    // ─────────────────────────────────────────────────────────────────────────

    public function openReminderModal(int $tenantId, string $name, int $amountDue): void
    {
        $this->selectedTenantId   = $tenantId;
        $this->selectedTenantName = $name;
        $this->selectedAmountDue  = $amountDue;
        $this->reminderNote       = '';
        $this->showReminderModal  = true;
    }

    public function sendReminder(): void
    {
        $this->validate(['reminderNote' => 'nullable|string|max:1000']);

        $tenant   = Tenant::findOrFail($this->selectedTenantId);
        $payments = $tenant->payments()
            ->whereIn('status', ['pending', 'failed'])
            ->orderBy('created_at')
            ->get();

        $daysOverdue = $payments->isNotEmpty()
            ? (int) now()->diffInDays($payments->first()->created_at)
            : 0;

        Mail::to($tenant->email)->send(new PaymentReminderMail(
            clubName:        $tenant->name,
            amountDue:       $this->selectedAmountDue,
            daysOverdue:     $daysOverdue,
            pendingPayments: $payments,
            note:            $this->reminderNote ?: null,
        ));

        PaymentReminder::create([
            'tenant_id'        => $tenant->id,
            'sent_by_user_id'  => auth()->id(),
            'amount_due'       => $this->selectedAmountDue,
            'note'             => $this->reminderNote ?: null,
        ]);

        TenantActivityLog::log(
            $tenant->id,
            'email_sent',
            'Relance de paiement envoyée. Montant dû : '.number_format($this->selectedAmountDue / 100, 0, ',', ' ').' XOF',
            ['amount_due' => $this->selectedAmountDue, 'to' => $tenant->email],
        );

        $this->showReminderModal = false;
        $this->dispatch('toast', message: 'Relance envoyée à '.$tenant->name.'.', type: 'success');
        unset($this->overdueClubs);
    }

    public function suspendClub(int $tenantId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update(['status' => 'suspended', 'suspended_at' => now()]);

        TenantActivityLog::log(
            $tenant->id,
            'suspended',
            'Club suspendu pour impayé depuis la page Impayés.',
            ['reason' => 'impayé'],
        );

        $this->dispatch('toast', message: $tenant->name.' suspendu.', type: 'success');
        unset($this->overdueClubs);
    }

    public function markAsPaid(int $tenantId): void
    {
        $tenant = Tenant::findOrFail($tenantId);

        $updated = $tenant->payments()
            ->whereIn('status', ['pending', 'failed'])
            ->update(['status' => 'paid', 'paid_at' => now()->toDateString()]);

        TenantActivityLog::log(
            $tenant->id,
            'payment',
            "Paiements marqués comme réglés manuellement ({$updated} paiement(s)).",
            ['count' => $updated, 'marked_by' => auth()->id()],
        );

        $this->dispatch('toast', message: $updated.' paiement(s) marqué(s) comme payé(s).', type: 'success');
        unset($this->overdueClubs);
    }

    public function openHistoryModal(int $tenantId, string $name): void
    {
        $this->selectedTenantId  = $tenantId;
        $this->historyTenantName = $name;
        $this->showHistoryModal  = true;
    }

    // ── Données calculées ────────────────────────────────────────────────────

    #[Computed]
    public function overdueClubs(): Collection
    {
        return Tenant::query()
            ->whereHas('payments', fn ($q) => $q->whereIn('status', ['pending', 'failed']))
            ->with([
                'plan',
                'payments' => fn ($q) => $q->whereIn('status', ['pending', 'failed'])->orderBy('created_at'),
                'paymentReminders' => fn ($q) => $q->latest('created_at')->with('sentBy'),
            ])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get()
            ->map(function (Tenant $tenant) {
                $oldestPayment = $tenant->payments->first();
                $daysOverdue   = $oldestPayment
                    ? (int) now()->diffInDays($oldestPayment->created_at)
                    : 0;
                $totalDue     = $tenant->payments->sum('amount');
                $urgency      = match (true) {
                    $daysOverdue > 15 => 'critical',
                    $daysOverdue >= 7 => 'warning',
                    default           => 'ok',
                };

                return (object) [
                    'tenant'        => $tenant,
                    'totalDue'      => $totalDue,
                    'daysOverdue'   => $daysOverdue,
                    'urgency'       => $urgency,
                    'lastReminder'  => $tenant->paymentReminders->first(),
                    'remindersCount' => $tenant->paymentReminders->count(),
                    'payments'      => $tenant->payments,
                ];
            })
            ->when(
                $this->urgencyFilter !== 'all',
                fn ($c) => $c->where('urgency', $this->urgencyFilter)
            )
            ->sortByDesc('daysOverdue')
            ->values();
    }

    #[Computed]
    public function reminderHistory(): Collection
    {
        if (! $this->selectedTenantId) {
            return collect();
        }

        return PaymentReminder::where('tenant_id', $this->selectedTenantId)
            ->with('sentBy')
            ->latest('created_at')
            ->get();
    }

    #[Computed]
    public function kpis(): array
    {
        $allOverdue = Tenant::query()
            ->whereHas('payments', fn ($q) => $q->whereIn('status', ['pending', 'failed']))
            ->withSum(['payments as total_due' => fn ($q) => $q->whereIn('status', ['pending', 'failed'])], 'amount')
            ->withMin(['payments as oldest_due_date' => fn ($q) => $q->whereIn('status', ['pending', 'failed'])], 'created_at')
            ->get();

        $totalAmount  = $allOverdue->sum('total_due');
        $criticalCount = 0;
        $warningCount  = 0;

        foreach ($allOverdue as $tenant) {
            $days = $tenant->oldest_due_date
                ? (int) now()->diffInDays($tenant->oldest_due_date)
                : 0;
            if ($days > 15) $criticalCount++;
            elseif ($days >= 7) $warningCount++;
        }

        return [
            'total_clubs'       => $allOverdue->count(),
            'total_amount'      => $totalAmount,
            'critical_count'    => $criticalCount,
            'warning_count'     => $warningCount,
            'reminders_month'   => PaymentReminder::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.superadmin.finance.unpaid');
    }
}
