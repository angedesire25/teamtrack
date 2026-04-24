<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id', 'user_id', 'first_name', 'last_name',
        'role', 'contract_type', 'contract_start', 'contract_end',
        'phone', 'email',
    ];

    protected function casts(): array
    {
        return ['contract_start' => 'date', 'contract_end' => 'date'];
    }

    public function tenant(): BelongsTo    { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }

    /** Nombre de jours avant l'expiration du contrat (null si pas de date) */
    public function daysUntilContractEnd(): ?int
    {
        return $this->contract_end?->diffInDays(now(), false) * -1;
    }

    /** Vrai si le contrat expire dans les 30 prochains jours */
    public function isContractExpiringSoon(): bool
    {
        if (! $this->contract_end) return false;
        $days = $this->daysUntilContractEnd();
        return $days !== null && $days >= 0 && $days <= 30;
    }
}
