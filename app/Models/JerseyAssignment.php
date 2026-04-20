<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JerseyAssignment extends Model
{
    protected $fillable = [
        'jersey_id', 'player_id', 'jersey_number', 'season',
        'assigned_at', 'returned_at', 'condition_returned', 'notes',
    ];

    protected $casts = [
        'assigned_at'  => 'date',
        'returned_at'  => 'date',
    ];

    public function jersey(): BelongsTo  { return $this->belongsTo(Jersey::class); }
    public function player(): BelongsTo  { return $this->belongsTo(Player::class); }

    public function isActive(): bool     { return $this->returned_at === null; }

    public function conditionReturnedLabel(): string
    {
        return match($this->condition_returned) {
            'good'    => 'Bon état',
            'damaged' => 'Endommagé',
            'lost'    => 'Perdu',
            default   => '—',
        };
    }
}
