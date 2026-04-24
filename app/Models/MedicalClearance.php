<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalClearance extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'player_id', 'status', 'reason',
        'effective_date', 'review_date', 'set_by_user_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'review_date'    => 'date',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by_user_id');
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'fit'         => 'Apte',
            'unfit'       => 'Inapte',
            'conditional' => 'Sous réserve',
            default       => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'fit'         => 'green',
            'unfit'       => 'red',
            'conditional' => 'yellow',
            default       => 'gray',
        };
    }
}
