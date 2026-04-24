<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = ['player_subscription_id', 'amount', 'payment_date', 'method', 'reference', 'notes'];

    protected function casts(): array
    {
        return ['payment_date' => 'date'];
    }

    public function subscription(): BelongsTo { return $this->belongsTo(PlayerSubscription::class, 'player_subscription_id'); }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'cash'          => 'Espèces',
            'mobile_money'  => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            'cheque'        => 'Chèque',
            'online'        => 'En ligne (Stripe)',
            default         => $this->method,
        };
    }
}
