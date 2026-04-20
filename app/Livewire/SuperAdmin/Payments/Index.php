<?php

namespace App\Livewire\SuperAdmin\Payments;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
#[Title('Paiements')]
class Index extends Component
{
    use WithPagination;

    // --- Filtres ---
    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterTenant = '';

    #[Url]
    public string $filterFrom = '';

    #[Url]
    public string $filterTo = '';

    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterTenant(): void { $this->resetPage(); }
    public function updatingFilterFrom(): void { $this->resetPage(); }
    public function updatingFilterTo(): void { $this->resetPage(); }

    // --- Formulaire d'ajout manuel ---
    public bool $showModal = false;

    #[Validate('required|exists:tenants,id')]
    public string $newTenantId = '';

    #[Validate('required|integer|min:1')]
    public string $newAmount = '';

    #[Validate('required|in:paid,pending,failed')]
    public string $newStatus = 'paid';

    #[Validate('nullable|string|max:50')]
    public string $newMethod = '';

    #[Validate('nullable|date')]
    public string $newPaidAt = '';

    #[Validate('nullable|string|max:500')]
    public string $newNote = '';

    public function openModal(): void
    {
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['newTenantId', 'newAmount', 'newStatus', 'newMethod', 'newPaidAt', 'newNote']);
    }

    /** Enregistre manuellement un paiement */
    public function savePayment(): void
    {
        $this->validate([
            'newTenantId' => 'required|exists:tenants,id',
            'newAmount'   => 'required|integer|min:1',
            'newStatus'   => 'required|in:paid,pending,failed',
            'newMethod'   => 'nullable|string|max:50',
            'newPaidAt'   => 'nullable|date',
            'newNote'     => 'nullable|string|max:500',
        ]);

        Payment::create([
            'tenant_id' => $this->newTenantId,
            'amount'    => (int) $this->newAmount,
            'status'    => $this->newStatus,
            'method'    => $this->newMethod ?: null,
            'paid_at'   => $this->newPaidAt ?: null,
            'note'      => $this->newNote ?: null,
        ]);

        $this->closeModal();
        session()->flash('success', 'Paiement enregistré avec succès.');
    }

    // -----------------------------------------------------------------------
    // Données
    // -----------------------------------------------------------------------

    /** Liste paginée des paiements */
    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        return Payment::with('tenant')
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterTenant, fn ($q) => $q->where('tenant_id', $this->filterTenant))
            ->when($this->filterFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->filterFrom))
            ->when($this->filterTo, fn ($q) => $q->whereDate('created_at', '<=', $this->filterTo))
            ->latest()
            ->paginate(20);
    }

    /** Clubs pour le filtre et le formulaire */
    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::orderBy('name')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('livewire.super-admin.payments.index', [
            'payments' => $this->payments,
            'tenants'  => $this->tenants,
        ]);
    }
}
