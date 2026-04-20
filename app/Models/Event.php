<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use TenantScoped, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'type', 'title', 'starts_at', 'ends_at',
        'field_id', 'team_id',
        'is_recurring', 'recurrence_until', 'parent_event_id',
        'competition', 'opponent', 'home_away',
        'score_home', 'score_away', 'match_report', 'convocations_sent',
        'notes',
    ];

    protected $casts = [
        'starts_at'          => 'datetime',
        'ends_at'            => 'datetime',
        'recurrence_until'   => 'date',
        'is_recurring'       => 'boolean',
        'convocations_sent'  => 'boolean',
        'score_home'         => 'integer',
        'score_away'         => 'integer',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function eventPlayers(): HasMany
    {
        return $this->hasMany(EventPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'event_players')
                    ->withPivot('status', 'lineup', 'position_played', 'minutes_played')
                    ->withTimestamps();
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'match'    => 'Match',
            'training' => 'Entraînement',
            'meeting'  => 'Réunion',
            'travel'   => 'Déplacement',
            default    => ucfirst($this->type),
        };
    }

    public function typeColor(): string
    {
        return match($this->type) {
            'match'    => '#EF4444',
            'training' => '#3B82F6',
            'meeting'  => '#8B5CF6',
            'travel'   => '#F59E0B',
            default    => '#6B7280',
        };
    }

    public function resultLabel(): ?string
    {
        if ($this->score_home === null || $this->score_away === null) {
            return null;
        }
        return $this->score_home . ' - ' . $this->score_away;
    }
}
