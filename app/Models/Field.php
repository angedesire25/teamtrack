<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'name', 'address', 'surface', 'capacity', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
