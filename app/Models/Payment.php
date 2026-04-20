<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'reference',
        'amount',
        'status',
        'method',
        'note',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'integer',
            'paid_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Génération automatique de la référence de paiement
        static::creating(function (Payment $payment) {
            $payment->reference = 'PAY-' . strtoupper(Str::random(8));
        });
    }

    // --- Relations ---

    /** Club concerné par ce paiement */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
