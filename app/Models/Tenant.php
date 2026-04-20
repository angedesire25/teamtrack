<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'email',
        'phone',
        'city',
        'country',
        'logo',
        'primary_color',
        'secondary_color',
        'status',
        'trial_ends_at',
        'suspended_at',
        'stripe_customer_id',
        'stripe_subscription_id',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'suspended_at'  => 'datetime',
        ];
    }

    // --- Relations ---

    /** Plan d'abonnement souscrit */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /** Utilisateurs appartenant à ce club */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Joueurs inscrits dans ce club */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /** Catégories (U7, U9, Senior...) définies par le club */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /** Équipes du club */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /** Paiements enregistrés pour ce club */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // --- Méthodes utilitaires ---

    /** Vérifie si le club est en état actif */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Vérifie si le club est suspendu */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
