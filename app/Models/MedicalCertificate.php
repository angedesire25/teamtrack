<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalCertificate extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id', 'player_id', 'certificate_type',
        'issued_at', 'expires_at', 'file_path', 'notes', 'uploaded_by_user_id',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function expiresWithinDays(int $days = 30): bool
    {
        if (!$this->expires_at || $this->isExpired()) return false;
        return $this->expires_at->diffInDays(now()) <= $days;
    }

    public function expiryStatus(): string
    {
        if (!$this->expires_at) return 'none';
        if ($this->isExpired()) return 'expired';
        if ($this->expiresWithinDays(30)) return 'soon';
        return 'valid';
    }

    public function typeLabel(): string
    {
        return match($this->certificate_type) {
            'aptitude'          => 'Aptitude',
            'contre-indication' => 'Contre-indication',
            'specialiste'       => 'Spécialiste',
            default             => 'Autre',
        };
    }
}
