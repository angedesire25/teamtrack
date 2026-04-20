<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'name', 'contact_name', 'email', 'phone', 'address', 'notes', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function jerseys(): HasMany    { return $this->hasMany(Jersey::class); }
    public function equipmentItems(): HasMany { return $this->hasMany(EquipmentItem::class); }
}
