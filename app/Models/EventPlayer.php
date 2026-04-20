<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlayer extends Model
{
    protected $fillable = [
        'event_id', 'player_id', 'status', 'lineup', 'position_played', 'minutes_played',
    ];

    protected $casts = [
        'minutes_played' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'convoked' => 'Convoqué',
            'present'  => 'Présent',
            'absent'   => 'Absent',
            'excused'  => 'Excusé',
            default    => ucfirst($this->status),
        };
    }

    public function lineupLabel(): string
    {
        return match($this->lineup) {
            'starter'    => 'Titulaire',
            'substitute' => 'Remplaçant',
            default      => '—',
        };
    }
}
