<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password',
        'is_super_admin', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    public function tenant(): BelongsTo     { return $this->belongsTo(Tenant::class); }
    public function staffProfiles(): HasMany { return $this->hasMany(Staff::class); }

    /** Libellé du rôle principal Spatie */
    public function primaryRoleLabel(): string
    {
        $labels = [
            'admin_club'         => 'Admin Club',
            'manager'            => 'Manager',
            'entraineur'         => 'Entraîneur',
            'staff_medical'      => 'Staff Médical',
            'secretaire'         => 'Secrétaire',
            'gestionnaire_stock' => 'Gestionnaire Stock',
            'comptable'          => 'Comptable',
        ];

        $role = $this->roles->first()?->name;
        return $labels[$role] ?? 'Utilisateur';
    }
}
