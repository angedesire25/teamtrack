<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'campaign_id', 'donor_id', 'amount', 'currency',
        'frequency', 'status', 'stripe_session_id', 'stripe_payment_intent_id',
        'stripe_subscription_id', 'is_anonymous', 'message',
        'receipt_number', 'receipt_sent_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'is_anonymous'     => 'boolean',
        'receipt_sent_at'  => 'datetime',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(DonationCampaign::class); }
    public function donor(): BelongsTo   { return $this->belongsTo(Donor::class); }

    public function isCompleted(): bool  { return $this->status === 'completed'; }

    public function frequencyLabel(): string
    {
        return match($this->frequency) {
            'one_time' => 'Unique',
            'monthly'  => 'Mensuel',
            'annual'   => 'Annuel',
            default    => ucfirst($this->frequency),
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'completed' => 'Complété',
            'failed'    => 'Échoué',
            'refunded'  => 'Remboursé',
            default     => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'completed' => 'text-emerald-600 bg-emerald-50',
            'failed'    => 'text-red-600 bg-red-50',
            'refunded'  => 'text-amber-600 bg-amber-50',
            default     => 'text-gray-600 bg-gray-50',
        };
    }

    public function donorName(): string
    {
        if ($this->is_anonymous) return 'Anonyme';
        return $this->donor?->fullName() ?? 'Inconnu';
    }

    public static function generateReceiptNumber(int $tenantId): string
    {
        $year    = now()->format('Y');
        $count   = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->count() + 1;
        return sprintf('REC-%d-%s-%04d', $tenantId, $year, $count);
    }
}
