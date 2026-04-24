<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'number',
        'tenant_id',
        'period_start',
        'period_end',
        'amount',
        'status',
        'plan_name',
        'plan_description',
        'notes',
        'sent_at',
        'paid_at',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
            'sent_at'      => 'datetime',
            'paid_at'      => 'date',
            'amount'       => 'integer',
        ];
    }

    // --- Relations ---

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // --- Méthodes utilitaires ---

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'Brouillon',
            'sent'      => 'Envoyée',
            'paid'      => 'Payée',
            'cancelled' => 'Annulée',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft'     => 'bg-gray-100 text-gray-600',
            'sent'      => 'bg-blue-100 text-blue-700',
            'paid'      => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-600',
            default     => 'bg-gray-100 text-gray-600',
        };
    }

    /** Génère le prochain numéro de facture séquentiel pour l'année en cours */
    public static function nextNumber(): string
    {
        $year  = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'FAC-'.$year.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
