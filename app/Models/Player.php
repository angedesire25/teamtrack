<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id', 'category_id', 'team_id',
        'first_name', 'last_name', 'birth_date', 'nationality',
        'position', 'jersey_number', 'foot', 'photo',
        'phone', 'email', 'emergency_contact', 'emergency_phone',
        'status', 'license_number', 'license_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'         => 'date',
            'license_expires_at' => 'date',
            'jersey_number'      => 'integer',
        ];
    }

    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function team(): BelongsTo     { return $this->belongsTo(Team::class); }

    public function documents(): MorphMany       { return $this->morphMany(Document::class, 'documentable'); }
    public function injuries(): HasMany          { return $this->hasMany(Injury::class); }
    public function medicalCertificates(): HasMany { return $this->hasMany(MedicalCertificate::class); }
    public function medicalClearances(): HasMany  { return $this->hasMany(MedicalClearance::class); }

    public function latestClearance(): HasOne
    {
        return $this->hasOne(MedicalClearance::class)->latestOfMany();
    }

    /** Libellé traduit du statut */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'active'      => 'Actif',
            'injured'     => 'Blessé',
            'suspended'   => 'Suspendu',
            'loaned'      => 'Prêté',
            'transferred' => 'Transféré',
            'former'      => 'Ancien joueur',
            default       => $this->status,
        };
    }

    /** Couleur Tailwind badge selon le statut */
    public function statusColor(): string
    {
        return match ($this->status) {
            'active'      => 'bg-emerald-100 text-emerald-700',
            'injured'     => 'bg-red-100 text-red-700',
            'suspended'   => 'bg-amber-100 text-amber-700',
            'loaned'      => 'bg-blue-100 text-blue-700',
            'transferred' => 'bg-purple-100 text-purple-700',
            'former'      => 'bg-gray-100 text-gray-600',
            default       => 'bg-gray-100 text-gray-600',
        };
    }

    /** Âge calculé depuis la date de naissance */
    public function age(): ?int
    {
        return $this->birth_date?->age;
    }
}
