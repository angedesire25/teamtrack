<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Injury extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id', 'player_id', 'injury_type', 'description',
        'start_date', 'estimated_return_date', 'actual_return_date',
        'treatment', 'status', 'reported_by_user_id',
    ];

    protected $casts = [
        'start_date'            => 'date',
        'estimated_return_date' => 'date',
        'actual_return_date'    => 'date',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function typeLabel(): string
    {
        return match($this->injury_type) {
            'musculaire'   => 'Musculaire',
            'osseuse'      => 'Osseuse',
            'ligamentaire' => 'Ligamentaire',
            'articulaire'  => 'Articulaire',
            'tendon'       => 'Tendon',
            default        => 'Autre',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'active'     => 'Active',
            'recovering' => 'En rééducation',
            'recovered'  => 'Guérie',
            default      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'active'     => 'red',
            'recovering' => 'yellow',
            'recovered'  => 'green',
            default      => 'gray',
        };
    }
}
