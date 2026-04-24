<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'description',
        'expires_at',
        'max_uses',
        'is_active',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'value'      => 'integer',
            'max_uses'   => 'integer',
            'is_active'  => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    // --- Relations ---

    public function uses(): HasMany
    {
        return $this->hasMany(CouponUse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // --- Méthodes utilitaires ---

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->max_uses !== null && $this->uses()->count() >= $this->max_uses;
    }

    public function statusLabel(): string
    {
        if (! $this->is_active) return 'Désactivé';
        if ($this->isExpired())  return 'Expiré';
        if ($this->isExhausted()) return 'Épuisé';
        return 'Actif';
    }

    public function statusColor(): string
    {
        return match ($this->statusLabel()) {
            'Actif'      => 'bg-emerald-100 text-emerald-700',
            'Expiré'     => 'bg-amber-100 text-amber-700',
            'Épuisé'     => 'bg-orange-100 text-orange-700',
            'Désactivé'  => 'bg-gray-100 text-gray-500',
            default      => 'bg-gray-100 text-gray-500',
        };
    }

    public function typeLabel(): string
    {
        return $this->type === 'percentage' ? 'Pourcentage' : 'Montant fixe';
    }

    public function formattedValue(): string
    {
        return $this->type === 'percentage'
            ? $this->value.'%'
            : number_format($this->value, 0, ',', ' ').' XOF';
    }

    public static function generateCode(int $length = 8): string
    {
        return strtoupper(Str::random($length));
    }
}
