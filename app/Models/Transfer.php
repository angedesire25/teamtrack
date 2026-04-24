<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use TenantScoped, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'player_id', 'player_name', 'direction', 'type', 'status',
        'counterpart_club', 'counterpart_contact',
        'asking_price', 'agreed_fee',
        'loan_duration_months', 'loan_start_date', 'loan_end_date',
        'clauses', 'notes',
        'search_position', 'search_age_min', 'search_age_max', 'search_budget_max', 'search_criteria',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'clauses'        => 'array',
            'loan_start_date'=> 'date',
            'loan_end_date'  => 'date',
            'finalized_at'   => 'datetime',
        ];
    }

    public function player(): BelongsTo         { return $this->belongsTo(Player::class); }
    public function negotiations(): HasMany      { return $this->hasMany(TransferNegotiation::class)->orderBy('date'); }

    public function playerDisplayName(): string
    {
        return $this->player?->fullName() ?? $this->player_name ?? '—';
    }

    public function directionLabel(): string
    {
        return $this->direction === 'outgoing' ? 'Sortant' : 'Entrant';
    }

    public function directionColor(): string
    {
        return $this->direction === 'outgoing' ? 'orange' : 'blue';
    }

    public function typeLabel(): string
    {
        return $this->type === 'permanent' ? 'Transfert définitif' : 'Prêt';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'listed'         => 'Sur liste',
            'negotiating'    => 'En négociation',
            'offer_received' => 'Offre reçue',
            'agreed'         => 'Accord trouvé',
            'finalized'      => 'Finalisé',
            'cancelled'      => 'Annulé',
            default          => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'listed'         => 'gray',
            'negotiating'    => 'blue',
            'offer_received' => 'amber',
            'agreed'         => 'emerald',
            'finalized'      => 'green',
            'cancelled'      => 'red',
            default          => 'gray',
        };
    }

    public static function statusFlow(): array
    {
        return ['listed', 'negotiating', 'offer_received', 'agreed', 'finalized'];
    }

    public function nextStatuses(): array
    {
        $flow = self::statusFlow();
        $idx  = array_search($this->status, $flow);

        if ($this->status === 'cancelled' || $this->status === 'finalized') {
            return [];
        }

        $nexts = [];
        if ($idx !== false && isset($flow[$idx + 1])) {
            $nexts[] = $flow[$idx + 1];
        }
        $nexts[] = 'cancelled';

        return $nexts;
    }
}
