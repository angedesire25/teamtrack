<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMovement extends Model
{
    protected $fillable = [
        'equipment_item_id', 'user_id', 'type', 'quantity',
        'reason', 'expected_return_at', 'returned_at', 'notes',
    ];

    protected $casts = [
        'quantity'          => 'integer',
        'expected_return_at'=> 'date',
        'returned_at'       => 'date',
    ];

    public function item(): BelongsTo { return $this->belongsTo(EquipmentItem::class, 'equipment_item_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function typeLabel(): string
    {
        return match($this->type) {
            'in'         => 'Entrée',
            'out'        => 'Sortie',
            'return'     => 'Retour',
            'adjustment' => 'Ajustement',
            default      => ucfirst($this->type),
        };
    }

    public function typeColor(): string
    {
        return match($this->type) {
            'in'         => 'text-emerald-600 bg-emerald-50',
            'out'        => 'text-red-600 bg-red-50',
            'return'     => 'text-blue-600 bg-blue-50',
            'adjustment' => 'text-amber-600 bg-amber-50',
            default      => 'text-gray-600 bg-gray-50',
        };
    }

    public function isOverdue(): bool
    {
        return $this->type === 'out'
            && $this->returned_at === null
            && $this->expected_return_at !== null
            && $this->expected_return_at->isPast();
    }
}
