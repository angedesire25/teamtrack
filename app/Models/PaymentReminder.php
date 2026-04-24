<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'sent_by_user_id',
        'amount_due',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'amount_due' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
