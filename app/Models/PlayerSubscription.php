<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerSubscription extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'player_id', 'plan_id', 'season',
        'amount_due', 'amount_paid', 'due_date', 'status',
        'last_reminder_at', 'stripe_session_id', 'stripe_checkout_url', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date'         => 'date',
            'last_reminder_at' => 'datetime',
        ];
    }

    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
    public function plan(): BelongsTo   { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
    public function payments(): HasMany  { return $this->hasMany(SubscriptionPayment::class); }

    public function amountRemaining(): int
    {
        return max(0, $this->amount_due - $this->amount_paid);
    }

    public function updateAmountAndStatus(): void
    {
        if ($this->status === 'exempted') {
            return;
        }

        $paid = $this->payments()->sum('amount');

        if ($paid >= $this->amount_due) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        } elseif ($this->due_date->isPast()) {
            $status = 'overdue';
        } else {
            $status = 'pending';
        }

        $this->update(['amount_paid' => $paid, 'status' => $status]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'En attente',
            'partial'  => 'Partiel',
            'paid'     => 'Payé',
            'overdue'  => 'En retard',
            'exempted' => 'Exonéré',
            default    => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'  => 'gray',
            'partial'  => 'amber',
            'paid'     => 'emerald',
            'overdue'  => 'red',
            'exempted' => 'blue',
            default    => 'gray',
        };
    }

    public function needsReminder(): bool
    {
        if (in_array($this->status, ['paid', 'exempted'])) {
            return false;
        }

        if (!$this->due_date->isPast() && $this->status === 'pending') {
            return false;
        }

        if ($this->last_reminder_at && $this->last_reminder_at->diffInDays(now()) < 7) {
            return false;
        }

        return $this->player?->email !== null;
    }
}
