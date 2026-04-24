<?php

namespace App\Livewire\SuperAdmin\Finance;

use App\Models\Coupon;
use App\Models\CouponUse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.superadmin', ['pageTitle' => 'Remises & Coupons'])]
class Discounts extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all'; // all | active | inactive | expired

    // ── Modal formulaire ────────────────────────────────────────────────────
    public bool  $showFormModal = false;
    public ?int  $editingId     = null;

    #[Validate('required|string|max:50|regex:/^[A-Z0-9_-]+$/')]
    public string $code = '';

    #[Validate('required|in:percentage,fixed')]
    public string $type = 'percentage';

    #[Validate('required|integer|min:1|max:100000')]
    public int    $value = 10;

    #[Validate('nullable|string|max:200')]
    public string $description = '';

    #[Validate('nullable|date|after:today')]
    public string $expiresAt = '';

    #[Validate('nullable|integer|min:1')]
    public string $maxUses = '';

    // ── Modal utilisations ──────────────────────────────────────────────────
    public bool   $showUsesModal      = false;
    public ?int   $selectedCouponId   = null;
    public string $selectedCouponCode = '';

    // ─────────────────────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->editingId    = null;
        $this->code         = '';
        $this->type         = 'percentage';
        $this->value        = 10;
        $this->description  = '';
        $this->expiresAt    = '';
        $this->maxUses      = '';
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $coupon = Coupon::findOrFail($id);

        $this->editingId    = $id;
        $this->code         = $coupon->code;
        $this->type         = $coupon->type;
        $this->value        = $coupon->value;
        $this->description  = $coupon->description ?? '';
        $this->expiresAt    = $coupon->expires_at?->format('Y-m-d') ?? '';
        $this->maxUses      = $coupon->max_uses ? (string) $coupon->max_uses : '';
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function generateCode(): void
    {
        $this->code = 'TT-'.Coupon::generateCode(6);
    }

    public function save(): void
    {
        $this->validate([
            'code'        => 'required|string|max:50|regex:/^[A-Z0-9_-]+$/|unique:coupons,code'.($this->editingId ? ','.$this->editingId : ''),
            'type'        => 'required|in:percentage,fixed',
            'value'       => 'required|integer|min:1'.($this->type === 'percentage' ? '|max:100' : ''),
            'description' => 'nullable|string|max:200',
            'expiresAt'   => 'nullable|date|after:today',
            'maxUses'     => 'nullable|integer|min:1',
        ]);

        $data = [
            'code'               => strtoupper($this->code),
            'type'               => $this->type,
            'value'              => $this->value,
            'description'        => $this->description ?: null,
            'expires_at'         => $this->expiresAt ?: null,
            'max_uses'           => $this->maxUses ?: null,
            'created_by_user_id' => auth()->id(),
        ];

        if ($this->editingId) {
            Coupon::findOrFail($this->editingId)->update($data);
            $message = 'Coupon mis à jour.';
        } else {
            Coupon::create(array_merge($data, ['is_active' => true]));
            $message = 'Coupon créé.';
        }

        $this->showFormModal = false;
        $this->dispatch('toast', message: $message, type: 'success');
        unset($this->coupons);
    }

    public function toggleActive(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => ! $coupon->is_active]);

        $label = $coupon->fresh()->is_active ? 'activé' : 'désactivé';
        $this->dispatch('toast', message: "Coupon {$coupon->code} {$label}.", type: 'success');
        unset($this->coupons);
    }

    public function delete(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $code   = $coupon->code;
        $coupon->delete();

        $this->dispatch('toast', message: "Coupon {$code} supprimé.", type: 'success');
        unset($this->coupons);
    }

    public function openUsesModal(int $id, string $code): void
    {
        $this->selectedCouponId   = $id;
        $this->selectedCouponCode = $code;
        $this->showUsesModal      = true;
    }

    // ── Données calculées ────────────────────────────────────────────────────

    #[Computed]
    public function coupons(): Collection
    {
        return Coupon::query()
            ->withCount('uses')
            ->with('createdBy')
            ->when($this->search, fn ($q) =>
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter === 'active', fn ($q) =>
                $q->where('is_active', true)
                  ->where(fn ($q2) => $q2->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            )
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->statusFilter === 'expired',  fn ($q) => $q->where('expires_at', '<', now()))
            ->latest()
            ->get();
    }

    #[Computed]
    public function couponUses(): Collection
    {
        if (! $this->selectedCouponId) {
            return collect();
        }

        return CouponUse::where('coupon_id', $this->selectedCouponId)
            ->with('tenant')
            ->latest('created_at')
            ->get();
    }

    #[Computed]
    public function kpis(): array
    {
        $total   = Coupon::count();
        $active  = Coupon::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
        $expired = Coupon::whereNotNull('expires_at')->where('expires_at', '<', now())->count();
        $uses    = CouponUse::count();

        return [
            'total'   => $total,
            'active'  => $active,
            'expired' => $expired,
            'uses'    => $uses,
        ];
    }

    public function render(): View
    {
        return view('livewire.superadmin.finance.discounts');
    }
}
