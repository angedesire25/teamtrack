<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Document extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id', 'documentable_type', 'documentable_id',
        'document_group_id', 'version', 'document_type',
        'title', 'file_path', 'file_name', 'file_size', 'mime_type',
        'expires_at', 'notes', 'uploaded_by_user_id',
        'signed_at', 'signed_by_user_id', 'signed_ip',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'signed_at'  => 'datetime',
        'version'    => 'integer',
        'file_size'  => 'integer',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Uniquement la dernière version de chaque groupe */
    public function scopeLatestVersions($query)
    {
        return $query->whereRaw('version = (
            SELECT MAX(d2.version) FROM documents d2
            WHERE d2.document_group_id = documents.document_group_id
            AND d2.deleted_at IS NULL
        )');
    }

    /** Documents dont l'expiration approche dans $days jours */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now())
            ->whereDate('expires_at', '<=', now()->addDays($days));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public static function newGroupId(): string
    {
        return (string) Str::uuid();
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

    public function isSigned(): bool
    {
        return $this->signed_at !== null;
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    public function formattedSize(): string
    {
        if (!$this->file_size) return '—';
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        $size = $this->file_size;
        while ($size >= 1024 && $i < 3) { $size /= 1024; $i++; }
        return round($size, 1).' '.$units[$i];
    }

    public function typeLabel(): string
    {
        return match($this->document_type) {
            'contrat'               => 'Contrat',
            'licence'               => 'Licence',
            'certificat_medical'    => 'Certificat médical',
            'autorisation_parentale'=> 'Autorisation parentale',
            'passeport'             => 'Passeport',
            default                 => 'Autre',
        };
    }

    public function typeBadgeColor(): string
    {
        return match($this->document_type) {
            'contrat'               => 'bg-blue-100 text-blue-700',
            'licence'               => 'bg-purple-100 text-purple-700',
            'certificat_medical'    => 'bg-emerald-100 text-emerald-700',
            'autorisation_parentale'=> 'bg-amber-100 text-amber-700',
            'passeport'             => 'bg-indigo-100 text-indigo-700',
            default                 => 'bg-gray-100 text-gray-600',
        };
    }

    public function entityLabel(): string
    {
        $entity = $this->documentable;
        if (!$entity) return '—';

        return match(true) {
            $entity instanceof Player => $entity->last_name.' '.$entity->first_name,
            $entity instanceof Staff  => $entity->last_name.' '.$entity->first_name,
            $entity instanceof Tenant => $entity->name,
            default                   => class_basename($entity).' #'.$entity->id,
        };
    }

    public function entityTypeLabel(): string
    {
        return match($this->documentable_type) {
            'App\Models\Player' => 'Joueur',
            'App\Models\Staff'  => 'Staff',
            'App\Models\Tenant' => 'Club',
            default             => 'Entité',
        };
    }
}
