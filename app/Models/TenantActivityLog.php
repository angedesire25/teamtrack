<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'event_type', 'description', 'meta', 'created_by_user_id', 'created_at',
    ];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo    { return $this->belongsTo(Tenant::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by_user_id'); }

    public function icon(): string
    {
        return match($this->event_type) {
            'created'       => 'M12 4v16m8-8H4',
            'payment'       => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            'suspended'     => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
            'activated'     => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'plan_changed'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'email_sent'    => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'login'         => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
            default         => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function color(): string
    {
        return match($this->event_type) {
            'created'      => 'text-blue-600 bg-blue-50',
            'payment'      => 'text-emerald-600 bg-emerald-50',
            'suspended'    => 'text-red-600 bg-red-50',
            'activated'    => 'text-emerald-600 bg-emerald-50',
            'plan_changed' => 'text-purple-600 bg-purple-50',
            'email_sent'   => 'text-indigo-600 bg-indigo-50',
            'login'        => 'text-gray-600 bg-gray-50',
            default        => 'text-gray-600 bg-gray-50',
        };
    }

    /** Enregistre un événement pour un tenant */
    public static function log(int $tenantId, string $type, string $description, array $meta = [], ?int $userId = null): self
    {
        return self::create([
            'tenant_id'           => $tenantId,
            'event_type'          => $type,
            'description'         => $description,
            'meta'                => $meta ?: null,
            'created_by_user_id'  => $userId ?? auth()->id(),
            'created_at'          => now(),
        ]);
    }
}
