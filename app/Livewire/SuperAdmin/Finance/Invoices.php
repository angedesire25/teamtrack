<?php

namespace App\Livewire\SuperAdmin\Finance;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\TenantActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin', ['pageTitle' => 'Factures'])]
class Invoices extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all'; // all | draft | sent | paid | cancelled

    // ── Modal génération de facture ──────────────────────────────────────────
    public bool   $showGenerateModal = false;
    public int    $tenantId          = 0;
    public string $periodStart       = '';
    public string $periodEnd         = '';
    public int    $amount            = 0;
    public string $planName          = '';
    public string $planDescription   = '';
    public string $notes             = '';

    // ── Réinitialise la pagination si les filtres changent ───────────────────
    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    // ── Auto-complétion depuis le plan du club sélectionné ───────────────────
    public function updatedTenantId(int $value): void
    {
        if ($value) {
            $tenant = Tenant::with('plan')->find($value);
            if ($tenant?->plan) {
                $this->planName        = $tenant->plan->name;
                $this->planDescription = 'Accès complet à la plateforme TeamTrack.';
                $this->amount          = $tenant->plan->price; // déjà en FCFA
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function openGenerateModal(): void
    {
        $this->tenantId        = 0;
        $this->periodStart     = now()->startOfMonth()->format('Y-m-d');
        $this->periodEnd       = now()->endOfMonth()->format('Y-m-d');
        $this->amount          = 0;
        $this->planName        = '';
        $this->planDescription = '';
        $this->notes           = '';
        $this->resetValidation();
        $this->showGenerateModal = true;
    }

    public function generateInvoice(): void
    {
        $this->validate([
            'tenantId'    => 'required|integer|exists:tenants,id',
            'periodStart' => 'required|date',
            'periodEnd'   => 'required|date|after_or_equal:periodStart',
            'amount'      => 'required|integer|min:1',
            'planName'    => 'nullable|string|max:100',
            'notes'       => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::create([
            'number'           => Invoice::nextNumber(),
            'tenant_id'        => $this->tenantId,
            'period_start'     => $this->periodStart,
            'period_end'       => $this->periodEnd,
            'amount'           => $this->amount,
            'status'           => 'draft',
            'plan_name'        => $this->planName ?: null,
            'plan_description' => $this->planDescription ?: null,
            'notes'            => $this->notes ?: null,
            'created_by_user_id' => auth()->id(),
        ]);

        TenantActivityLog::log(
            $this->tenantId,
            'payment',
            'Facture '.$invoice->number.' générée (brouillon). Montant : '.number_format($invoice->amount / 100, 0, ',', ' ').' XOF',
            ['invoice_number' => $invoice->number, 'amount' => $invoice->amount],
        );

        $this->showGenerateModal = false;
        $this->dispatch('toast', message: 'Facture '.$invoice->number.' créée.', type: 'success');
    }

    public function sendInvoice(int $id): void
    {
        $invoice = Invoice::with('tenant')->findOrFail($id);

        Mail::to($invoice->tenant->email)->send(new InvoiceMail($invoice));

        $invoice->update(['status' => 'sent', 'sent_at' => now()]);

        TenantActivityLog::log(
            $invoice->tenant_id,
            'email_sent',
            'Facture '.$invoice->number.' envoyée par email à '.$invoice->tenant->email,
            ['invoice_number' => $invoice->number, 'to' => $invoice->tenant->email],
        );

        $this->dispatch('toast', message: 'Facture envoyée à '.$invoice->tenant->name.'.', type: 'success');
    }

    public function markAsPaid(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'paid', 'paid_at' => now()->toDateString()]);

        TenantActivityLog::log(
            $invoice->tenant_id,
            'payment',
            'Facture '.$invoice->number.' marquée comme payée.',
            ['invoice_number' => $invoice->number],
        );

        $this->dispatch('toast', message: 'Facture marquée comme payée.', type: 'success');
    }

    public function cancelInvoice(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'cancelled']);

        $this->dispatch('toast', message: 'Facture '.$invoice->number.' annulée.', type: 'success');
    }

    // ── Données calculées ────────────────────────────────────────────────────

    #[Computed]
    public function invoices(): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['tenant', 'createdBy'])
            ->when($this->search, fn ($q) =>
                $q->where('number', 'like', "%{$this->search}%")
                  ->orWhereHas('tenant', fn ($q2) => $q2->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function kpis(): array
    {
        return [
            'total'   => Invoice::count(),
            'draft'   => Invoice::where('status', 'draft')->count(),
            'sent'    => Invoice::where('status', 'sent')->count(),
            'paid'    => Invoice::where('status', 'paid')->count(),
            'revenue' => Invoice::where('status', 'paid')->sum('amount'),
        ];
    }

    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::orderBy('name')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('livewire.superadmin.finance.invoices');
    }
}
