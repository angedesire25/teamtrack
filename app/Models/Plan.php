<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_cycle',
        'max_players',
        'max_users',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'integer',
            'max_players' => 'integer',
            'max_users'  => 'integer',
            'features'   => 'array',
            'is_active'  => 'boolean',
        ];
    }

    // --- Relations ---

    /** Clubs souscripteurs à ce plan */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
