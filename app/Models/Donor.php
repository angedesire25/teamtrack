<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'country', 'stripe_customer_id',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function completedDonations(): HasMany
    {
        return $this->hasMany(Donation::class)->where('status', 'completed');
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function totalDonated(): float
    {
        return (float) $this->completedDonations()->sum('amount');
    }
}
